<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Services\OpenFDA;
use App\Services\RXNorm;
use App\Services\RXClass;
use App\Facades\Drug;
use App\Facades\DrugIndication;
use App\Facades\DrugSideEffect;

class ImportDrugs extends Command
{
    private $concepts = [];

    private $indications;
    private $side_effects;
    private $translation;

    private $openfda;
    private $rxnorm;
    private $rxclass;

    private $limit;
    private $skip;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:drugs' .
        ' {--limit=0 : limit the number of brands to be imported}' .
        ' {--skip=0 : skip a number of brands from the beginning}' .
        ' {--debug : do not save in db, print values in console}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import drug data from various APIs such as OpenFDA, RxNorm & RxClass';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OpenFDA $openfda, RXNorm $rxnorm, RXClass $rxclass)
    {
        parent::__construct();

        $this->openfda = $openfda;
        $this->rxnorm = $rxnorm;
        $this->rxclass = $rxclass;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting drug import... ');

        Model::unguard(); // allow mass fills

        if ($this->option('verbose')) {
            $this->openfda->setVerbose(true);
            $this->rxnorm->setVerbose(true);
            $this->rxnorm->setVerbose(true);
        }

        $this->limit = (int)$this->option('limit');
        $this->skip = (int)$this->option('skip');

        $this->indications = DrugIndication::lists('id', 'value');
        $this->side_effects = DrugSideEffect::lists('id', 'value');
        $this->translation = $this->setupTranslation();

        $brands = $this->rxnorm->getAllBrands();

        $total = ($this->limit ? : count($brands) - $this->skip);
        $this->info("Importing {$total} brands" . ($this->skip ? ' skipping ' . $this->skip : '') . ' ...');

        $imported = 0;
        foreach ($brands as $brand) {
            if ($this->skip-- <= 0) {
                $imported++;
                $this->importConcept($brand['rxcui']);

                if (!$this->option('debug') && $imported % 100  === 0) {
                    $this->line("Imported {$imported} (out of {$total}) concepts so far ...");
                }
                if (!empty($this->limit) && $imported >= $this->limit) {
                    break;
                }
            }
        }

        $total = count($this->concepts);
        $this->info("Imported {$total} concepts. Importing alternatives ...");

        $imported = 0;
        foreach ($this->concepts as $rxcui => $concept) {
            $imported++;
            $this->importAlternatives($rxcui, $concept);

            if (!$this->option('debug') && $imported % 100 == 0) {
                $this->line("Imported {$imported} (out of {$total}) alternatives so far ...");
            }
        }

        $this->info('Drug Import Done!');
    }

    private function importConcept($rxcui)
    {
        static $id = 1;

        if ($concept = $this->getConceptValues($rxcui)) {
            $drug = Drug::firstOrNew(['rxcui' => $rxcui]);

            $drug->label = $concept['label'];
            $drug->generic = $concept['generic'];
            $drug->drug_forms = $concept['drug_forms'];
            $drug->description = $concept['description'];
            $drug->recalls = $concept['recalls'];

            if ($this->option('debug')) {
                $drug->id = $id++;

                $this->comment("\n==============================================================================");
                $this->comment("Importing rxcui: [{$rxcui}] as drug: [{$drug->id}] label: [{$drug->label} ({$drug->generic})]");
                $this->comment("==============================================================================\n");

                $print = $drug->toArray();
                $print['drug_forms'] = '[' . join(',', $print['drug_forms']) . ']';
                $print['prescription_types'] = '[' . join(',', $concept['types']) . ']';
                $print['indications'] = '[' . join(',', $concept['indications']) . ']';
                $print['side_effects'] = '[' . join(',', $concept['side_effects']) . ']';
                $print['related'] = '[' . join(',', $concept['related']) . ']';
                $this->line(json_encode($print, JSON_PRETTY_PRINT));
            } else {
                $drug->save();

                $drug->prescriptionTypes()->sync($concept['types']);

                if (!empty($concept['side_effects'])) {
                    $side_effects = array_unique(array_merge($concept['side_effects'], $drug->sideEffects->lists('id')->all()));
                    $drug->sideEffects()->sync($side_effects);
                }
                if (!empty($concept['indications'])) {
                    $indications = array_unique(array_merge($concept['indications'], $drug->indications->lists('id')->all()));
                    $drug->indications()->sync($indications);
                }
            }

            $this->concepts[$rxcui] = ['drug' => $drug, 'related' => $concept['related']];
        }
    }

    private function importAlternatives($rxcui, $concept)
    {
        $alternatives = [];

        foreach ($concept['related'] as $related) {
            if ($related != $rxcui && !empty($this->concepts[$related])) {
                $alternatives[] = $this->concepts[$related]['drug']->id;
            }
        }

        $drug = $concept['drug'];

        if ($this->option('debug')) {
            $this->comment("Alternatives for {$drug->id} ({$drug->rxcui}) [{$drug->label}] : " . join(',', $alternatives));
        } else {
            $drug->alternatives()->sync($alternatives);
        }
    }

    private function getConceptValues($rxcui)
    {
        $values = [];

        $concept = $this->rxnorm->getConceptProperties($rxcui);
        if (!empty($concept)) {
            $ttys = $this->rxnorm->getConceptRelations($rxcui);

            $values['rxcui'] = $rxcui;
            $values['label'] = (empty($concept['synonym']) ? $concept['name'] : $concept['synonym']);
            $values['drug_forms'] = array_get($ttys, 'DF.names', []);
            $values['generic'] = '';
            $values['ingredients'] = [];

            if ($ids = array_get($ttys, 'MIN.ids', [])) {
                $values['ingredients'] = $ids;
                $values['generic'] = join(' / ', array_get($ttys, 'MIN.names', []));
            } elseif ($ids = array_get($ttys, 'PIN.ids', [])) {
                $values['ingredients'] = $ids;
                $values['generic'] = join(' / ', array_get($ttys, 'PIN.names', []));
            } elseif ($ids = $values['ingredients'] = array_get($ttys, 'IN.ids', [])) {
                $values['ingredients'] = $ids;
                $values['generic'] = join(' / ', array_get($ttys, 'IN.names', []));
            }

            if ($pos = strpos($values['label'], 'Pack [')) {
                $values['label'] = substr($values['label'], $pos + 6, -1) . ' [Pack]';
            }

            $drugs = array_get($ttys, 'SBD.ids', []);
            $label = $this->openfda->getLabel($values, $drugs);
            $values['description'] = $label['description'];
            $values['types'] = $label['prescription_types'];

            $drugs = array_slice(array_merge($drugs, array_get($ttys, 'SCD.ids', [])), 0, 20);
            $values['recalls'] = $this->openfda->getRecalls($drugs);
            $values['indications'] = $this->getIndications($drugs);
            $values['side_effects'] = $this->getSideEffects($drugs);

            $values['related'] = $this->getRelatedConcepts($concept, $values['ingredients']);
        }

        return $values;
    }

    private function getRelatedConcepts($concept, $ingredients)
    {
        $concepts = [];

        foreach ($ingredients as $ingredient) {
            $concepts = array_merge($concepts, $this->rxnorm->getRelatedConcepts($ingredient));
        }

        if (count($concepts) < 5) {
            foreach ($ingredients as $ingredient) {
                foreach ($this->rxclass->getRelatedConcepts($ingredient) as $related_ingredient) {
                    $concepts = array_merge($concepts, $this->rxnorm->getRelatedConcepts($related_ingredient));
                }
            }
        }

        return array_slice(array_unique($concepts), 0, 20);
    }

    private function getIndications($drug_components)
    {
        $indications = [];

        $terms = $this->openfda->getIndications($drug_components);
        foreach ($terms as $term => $count) {
            if (count($indications) >= 20 || $count < 5) {
                break;
            }

            if ($indication = $this->translate($term)) {
                $id = $this->indications->get($indication);
                if (empty($id)) {
                    if ($this->option('debug')) {
                        $id = $this->indications->max() + 1;
                        $this->comment("New indication object: id [{$id}] value [{$indication}]");
                    } else {
                        $id = DrugIndication::create(['value' => $indication])->id;
                    }
                    $this->indications->put($indication, $id);
                }

                $indications[] = $id;
            }
        }

        return array_unique($indications);
    }

    private function getSideEffects($drug_components)
    {
        $side_effects = [];

        $terms = $this->openfda->getSideEffects($drug_components);
        foreach ($terms as $term => $count) {
            if (count($side_effects) >= 20 || $count < 5) {
                break;
            }

            if ($side_effect = $this->translate($term)) {
                $id = $this->side_effects->get($side_effect);
                if (empty($id)) {
                    if ($this->option('debug')) {
                        $id = $this->side_effects->max() + 1;
                        $this->comment("New side effect object: id [{$id}] value [{$side_effect}]");
                    } else {
                        $id = DrugSideEffect::create(['value' => $side_effect])->id;
                    }
                    $this->side_effects->put($side_effect, $id);
                }

                $side_effects[] = $id;
            }
        }

        return array_unique($side_effects);
    }

    private function translate($term)
    {
        $translation = '';

        if ($term) {
            $term = strtolower($term);
            if (!array_get($this->translation['skip'], $term, '')) {
                $translation = array_get($this->translation, "translations.{$term}", '');
                if (empty($translation)) {
                    $translation = $term;

                    $comma = strpos($translation, ', ');
                    if ($comma !== false) {
                        $translation = substr($translation, $comma + 2) . ' ' . substr($translation, 0, $comma);
                    }

                    foreach ($this->translation['fix_order'] as $word) {
                        $pos = strpos($term, ' ' . $word);
                        if ($pos !== false) {
                            $translation = $word . ' ' . substr($translation, 0, $pos);
                        }
                    }

                    foreach ($this->translation['replace'] as $from => $to) {
                        $translation = str_replace($from, $to, $translation);
                    }
                }
            }
        }

        return $translation;
    }

    private function setupTranslation()
    {
        return [
            'skip' => [
                'accidental drug intake by child' => true,
                'adverse event' => true,
                'condition aggravated' => true,
                'drug administration error' => true,
                'drug dose omission' => true,
                'drug exposure during pregnancy' => true,
                'drug ineffective' => true,
                'drug interaction' => true,
                'drug use for unknown indication' => true,
                'expired drug administered' => true,
                'fall' => true,
                'general physical health deterioration' => true,
                'ill-defined disorder' => true,
                'incorrect dose administered' => true,
                'incorrect drug administration duration' => true,
                'incorrect route of drug administration' => true,
                'increased international normalised ratio' => true,
                'infusion related reaction' => true,
                'intentional drug misuse' => true,
                'intentional misuse' => true,
                'intentional overdose' => true,
                'maternal exposure during pregnancy' => true,
                'medication error' => true,
                'miosis' => true,
                'no adverse event' => true,
                'off label use' => true,
                'pharmaceutical product complaint' => true,
                'post procedural complication' => true,
                'premedication' => true,
                'product quality issue' => true,
                'product substitution issue' => true,
                'product used for unknown indication' => true,
                'therapeutic response unexpected' => true,
                'unevaluable event' => true,
                'unresponsive to stimuli' => true,
                'wrong drug administered' => true,
                'wrong technique in drug usage process' => true,
                ],
            'translations' => [
                'abdominal pain lower' => 'abdominal pain',
                'abdominal pain upper' => 'abdominal pain',
                'acquired immunodeficiency syndrome' => 'aids',
                'ageusia' => 'loss of taste',
                'amyotrophic lateral sclerosis' => 'als',
                'anaemia' => 'anemia',
                'ankylosing spondylitis' => 'inflammatory arthritis',
                'arthalgia' => 'joint pain',
                'ascites' => 'abdominal swelling',
                'asthenia' => 'weakness',
                'attention deficit/hyperactivity disorder' => 'adhd',
                'bacterial gram-negative' => 'bacterial infection',
                'breast cancer female' => 'breast cancer',
                'confusional state' => 'confusion',
                'colitis ulcerative' => 'inflammatory bowel disease',
                'completed suicide' => 'suicidal ideation',
                'diabetes mellitus non-insuling-dependent' => 'non insulin dependent diabetes',
                'diarrhoea' => 'diarrhea',
                'diplaopia' => 'double vision',
                'drug abuser' => 'drug abuse',
                'dysgeusia' => 'loss of taste',
                'dyspepsia' => 'indigestion',
                'dyspnoea' => 'labored breathing',
                'endophthalmitis' => 'eye inflammation',
                'epistaxis' => 'nose bleed',
                'erythema' => 'skin redness',
                'faeces discoloured' => 'discolored feces',
                'fissure in ano' => 'anal fissure',
                'foetal exposure during pregnancy' => 'fetal exposure',
                'gastrooesophageal reflux disease' => 'acid reflux disease',
                'gram-positive bacterial infections' => 'bacterial infection',
                'hiv infection' => 'aids',
                'hypercholasterolaemia' => 'increased cholesterol',
                'hyperglycaemia' => 'hyperglycemia',
                'hyperhidrosis' => 'excessive sweating',
                'hyperlipidaemia' => 'elevated lipids',
                'hypoaesthesia' => 'numbness',
                'low density lipoprotein increased' => 'increased ldl',
                'multiple drug overdose' => 'overdose',
                'myalgia' => 'muscle pain',
                'nasopharyngitis' => 'nose and throat infection',
                'neuropathy peripheral' => 'peripheral neuropathy',
                'neutropenia' => 'neutrophil deficiency',
                'oedema peripheral' => 'peripheral edema',
                'osteoporosis postmenopausal' => 'postmenupausal osteoporosis',
                'paraesthesia' => 'numbness',
                'plearal effusion' => 'chest fluid buildup',
                'pruritis' => 'itching skin',
                'psychotic disorder' => 'psychosis',
                'psoriatic arthropathy' => 'psoriatic arthritis',
                'pyrexia' => 'fever',
                'rash maculo-papular' => 'rash',
                'rhabdomyolysis' => 'muscle tissue breakdown',
                'seborrheic dermatitis' => 'scaly skin patches',
                'sleep initiation and maintenance disorders' => 'sleep disorder',
                'somnolence' => 'drowsiness',
                'status asthmaticus' => 'asthma',
                'suicide attempt' => 'suicidal ideation',
                'thrombocytopenia' => 'low platelet count',
                'tobacco user' => 'tobacco abuse',
                'toxicity to various agents' => 'toxicity',
                'transient ischaemic attack' => 'stroke',
                'urticaria' => 'skin rash',
                'urinary tract infection' => 'uti',
                'wound infection staphylococcal' => 'wound infection',
                'xanthopsia' => 'vision deficiency',
                'xerosis' => 'dry skin',
                ],
            'replace' => [
                'anaemia' => 'anemia',
                'diarrhoea' => 'diarrhea',
                'discolouration' => 'discoloration',
                'haemorhage' => 'hemorhage',
                'increased blood' => 'increased',
                'leukaemia' => 'leukemia',
                'odour' => 'odor',
                'oedema' => 'edema',
                'oesophageal' => 'esophageal',
                ],
            'fix_order' => [
                'abnormal',
                'acute',
                'blurred',
                'chronic',
                'congestive',
                'decreased' ,
                'identified',
                'impaired',
                'increased',
                'induced',
                'malignant',
                'metastatic',
                'peripheral',
                'poor',
                'reduced',
                'spontaneous',
                ],
            ];
    }
}
