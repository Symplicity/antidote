<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use App\Services\OpenFDA;
use App\Services\RXNorm;
use App\Services\RXClass;
use App\Facades\Drug;
use App\Facades\DrugIndication;
use App\Facades\DrugSideEffect;

class ImportDrugs extends Command
{
    private $limit = 0; //unlimited
    private $skip = 0; //do not skip

    private $concepts = [];
    private $indications = [];
    private $side_effects = [];
    private $translation = [];
    
    private $openfda;
    private $rxnorm;
    private $rxclass;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:drugs' .
        ' {--limit=0 : limit the number of brands to be imported}' .
        ' {--skip=0 : skip a number of brands from the beginning}' .
        ' {--debug : do not save in db}';
    
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

        $this->skip = (int)$this->option('skip');
        $this->limit = (int)$this->option('limit');
        
        if ($this->option('verbose')) {
            $this->openfda->setVerbose(true);
            $this->rxnorm->setVerbose(true);
            $this->rxnorm->setVerbose(true);
        }

        $this->indications = DrugIndication::lists('id', 'value');
        $this->side_effects = DrugSideEffect::lists('id', 'value');
        
        $this->translation = $this->setupTranslation();
        
        $this->importConcepts();
        $this->importConceptRelations();
        
        $this->info('Drug Import Done!');
    }

    private function getConcept($rxcui)
    {
        $concept = ['rxcui' => $rxcui, 'type' => 'generic'];
        
        $properties = $this->rxnorm->getConceptProperties($rxcui);
        if (!empty($properties)) {
            $concept['label'] = $this->rxnorm->getConceptLabel($properties);
            $concept['sanitized_brand'] = $this->openfda->sanitizeName($concept['label']);
            $concept['generic'] = $concept['label'];
            $concept['sanitized_generic'] = $concept['sanitized_brand'];
            $concept['generic_id'] = '';
            
            //related, dose_forms, ingredients, ingredient, drug_components
            $concept['relations'] = $this->rxnorm->getConceptRelations($properties);
            
            $labels = $this->getLabels($concept);
            $concept['description'] = $this->getConceptDescription($concept, $labels);
            $concept['relations']['prescription_types'] = $this->getPrescriptionTypes($concept, $labels);
            
            $concept['relations']['recalls'] = $this->getRecalls($concept);
        }
        
        return $concept;
    }
    
    private function importConcept($concept)
    {
        static $id = 1;
        
        $rxcui = $concept['rxcui'];
        $drug = Drug::firstOrNew(['rxcui' => (int)$rxcui]);

        $drug->label = ucwords($concept['label']);
        $drug->generic = ucwords($concept['generic']);
        $drug->type = $concept['type'];
        $drug->generic_id = $concept['generic_id'];
        $drug->description = $concept['description'];
        $drug->drug_forms = array_get($concept, 'relations.dose_forms', []);
        $drug->recalls = $concept['relations']['recalls'];

        if ($this->option('debug')) {
            $drug->id = $id++;
        } else {
            $drug->save();
        }
               
        $concept['drug'] = $drug;
        return $concept;
    }

    private function importConcepts()
    {
        $brands = $this->rxnorm->getAllBrands();
        $brand_count = count($brands);

        $this->info("Importing " .
                    ($this->limit ? "{$this->limit} brands out of {$brand_count}" : "{$brand_count} brands").
                    ($this->skip ? " skipping {$this->skip}" : "") . " ...");
        
        foreach ($brands as $brand) {
            $rxcui = $brand['rxcui'];
            
            if ($this->skip-- <= 0) {
                $this->concepts[$rxcui] = $this->getConcept($rxcui);
                $this->concepts[$rxcui]['type'] = 'brand';
                
                $this->concepts[$rxcui]['generic'] = '';
                $this->concepts[$rxcui]['sanitized_generic'] = '';
                
                if ($irxcui = array_get($this->concepts, $rxcui . '.relations.ingredient', false)) {
                    if (empty($this->concepts[$irxcui])) {
                        $this->concepts[$irxcui] = $this->getConcept($irxcui);
                        $this->concepts[$irxcui] = $this->importConcept($this->concepts[$irxcui]);
                    }
                    
                    $this->concepts[$rxcui]['generic_id'] = $irxcui;
                    $this->concepts[$rxcui]['generic'] = $this->concepts[$irxcui]['label'];
                    $this->concepts[$rxcui]['sanitized_generic'] = $this->concepts[$irxcui]['sanitized_generic'];
                }
                
                $this->concepts[$rxcui] = $this->importConcept($this->concepts[$rxcui]);
                
                $total = count($this->concepts);
                if ($total % 100 === 0) {
                    $this->info('Imported ' . $total . ' drugs so far');
                }
                if ($this->limit && $total >= $this->limit) {
                    break;
                }
            }
        }
    }

    private function printDrug($drug, $relations)
    {
        $this->info("\n==============================================================================");
        $this->info("Importing rxcui: [{$drug['rxcui']}] as drug: [{$drug['id']}] label: [{$drug['label']}]");
        $this->info("==============================================================================\n");

        $drug['drug_forms'] = '[' . join(',', $drug['drug_forms']) . ']';
        $drug['prescription_types'] = '[' . join(',', $relations['types']) . ']';
        $drug['related'] = '[' . join(',', $relations['related']) . ']';
        $drug['alternatives'] = '[' . join(',', $relations['alternatives']) . ']';
        $drug['indications'] = '[' . join(',', $relations['indications']) . ']';
        $drug['side_effects'] = '[' . join(',', $relations['side_effects']) . ']';
        $drug['recalls'] = '[' . join(',', $drug['recalls']) . ']';
        print_r($drug);
    }

    private function importConceptRelations()
    {
        $this->info('Imported ' . count($this->concepts) . ' concepts. Starting relations import ...');
        
        foreach ($this->concepts as $rxcui => $concept) {
            $drug = $concept['drug'];
            $relations = [
                'types' => $concept['relations']['prescription_types'],
                'related' => $this->getRelated($concept),
                'alternatives' => $this->getAlternatives($concept),
                'indications' => $this->getIndications($concept),
                'side_effects' => $this->getSideEffects($concept)
                ];

            if (empty($this->option('debug'))) {
                $drug->prescriptionTypes()->sync($relations['types']);
                
                $drug->related()->sync($relations['related']);
                $drug->alternatives()->sync($relations['alternatives']);
                
                if (!empty($relations['indications'])) {
                    $drug->indications()->attach($relations['indications']);
                }
                if (!empty($relations['side_effects'])) {
                    $drug->sideEffects()->attach($relations['side_effects']);
                }
            }

            $this->printDrug($drug->toArray(), $relations);
        }
    }

    private function getRelated($concept)
    {
        $related = [];
        
        foreach (array_get($concept, 'relations.related', []) as $rxcui) {
            if ($rc = array_get($this->concepts, $rxcui, false)) {
                $related[] = $rc['drug']->id;
            }
        }
        
        return $related;
    }
    
    private function getAlternatives($concept)
    {
        $alternatives = [];
        
        foreach (array_get($concept, 'relations.ingredients', []) as $ingredient) {
            $related = $this->rxclass->getRelatedConcepts($ingredient);
            foreach ($related as $rel) {
                $brands = $this->rxnorm->getRelatedBrands($rel);
                foreach ($brands as $rxcui) {
                    if (empty($alternatives[$rxcui]) && $rc = array_get($this->concepts, $rxcui, false)) {
                        $alternatives[$rxcui] = $rc['drug']->id;
                    }
                }
            }
        }

        return $alternatives;
    }
    
    private function getIndications($concept)
    {
        $terms = [];
        foreach (array_get($concept, 'relations.ingredients', []) as $ingredient) {
            $terms = $this->rxclass->getConceptIndications($ingredient);
        }
        if (empty($indications)) {
            $terms = $this->openfda->getIndications($concept);
        }
        $terms = array_slice($terms, 0, 20);

        $indications = [];
        foreach ($terms as $term) {
            if ($indication = $this->translate($term)) {
                $id = $this->indications->get($indication);
                
                if (!$id) {
                    if ($this->option('debug')) {
                        $id = $this->indications->max() + 1;
                    } else {
                        $id = DrugIndication::create(['value' => $indication])->id;
                    }
                    $this->indications->put($indication, $id);
                    $this->comment("New indication object: id [{$id}] value [{$indication}]");
                }
                
                $indications[] = $id;
            }
        }
        
        return $indications;
    }

    private function getSideEffects($concept)
    {
        $terms = array_slice($this->openfda->getSideEffects($concept), 0, 20);
        
        $side_effects = [];
        foreach ($terms as $term) {
            if ($side_effect = $this->translate($term)) {
                $id = $this->side_effects->get($side_effect);
                
                if (!$id) {
                    if ($this->option('debug')) {
                        $id = $this->side_effects->max() + 1;
                    } else {
                        $id = DrugSideEffect::create(['value' => $side_effect])->id;
                    }
                    $this->side_effects->put($side_effect, $id);
                    $this->comment("New side effect object: id [{$id}] value [{$side_effect}]");
                }
                
                $side_effects[] = $id;
            }
        }
        
        return $side_effects;
    }

    private function getLabels($concept)
    {
        return $this->openfda->getDrugLabels($concept);
    }

    private function getConceptDescription($concept, $labels)
    {
        return $this->openfda->getDescription($concept, $labels);
    }
    
    private function getPrescriptionTypes($concept, $labels)
    {
        return $this->openfda->getPrescriptionTypes($concept, $labels);
    }
    
    private function getRecalls($concept)
    {
        return $this->openfda->getRecalls($concept);
    }

    private function translate($term)
    {
        $translation = '';

        if ($term && ($term = strtolower($term)) && !in_array($term, $this->translation['skip'])) {
            $translation = array_get($this->translation, 'translations.' . $term, '');
            if (!$translation) {
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
        
        return $translation;
    }

    private function setupTranslation()
    {
        return [
            'skip' => [
                'accidental drug intake by child',
                'adverse event',
                'condition aggravated',
                'drug administration error',
                'drug dose omission',
                'drug exposure during pregnancy',
                'drug ineffective',
                'drug interaction',
                'drug use for unknown indication',
                'expired drug administered',
                'fall',
                'general physical health deterioration',
                'ill-defined disorder',
                'incorrect dose administered',
                'incorrect drug administration duration',
                'incorrect route of drug administration',
                'increased international normalised ratio',
                'infusion related reaction',
                'intentional drug misuse',
                'intentional misuse',
                'intentional overdose',
                'maternal exposure during pregnancy',
                'medication error',
                'miosis',
                'no adverse event',
                'off label use',
                'pharmaceutical product complaint',
                'post procedural complication',
                'premedication',
                'product quality issue',
                'product substitution issue',
                'product used for unknown indication',
                'therapeutic response unexpected',
                'unevaluable event',
                'unresponsive to stimuli',
                'wrong drug administered',
                'wrong technique in drug usage process',
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
                ]
            ];
    }
}
