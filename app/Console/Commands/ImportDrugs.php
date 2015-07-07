<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Services\OpenFDA;
use App\Services\RXNorm;
use App\Services\RXClass;
use App\Services\MedicalTranslator;
use App\Facades\Drug;
use App\Facades\DrugIndication;
use App\Facades\DrugSideEffect;

class ImportDrugs extends Command
{
    private $concepts = [];

    private $indications;
    private $side_effects;
    private $translator;

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
        ' {--limit=0 : (optional) limit the number of brands to be imported}' .
        ' {--skip=0 : (optional) skip a number of brands from the beginning}' .
        ' {--debug : (optional) do not save in db, print values in console}' .
        ' {--ids= : (optional) csv string of drug rxcuis to be imported} ' .
        ' {--names= : (optional) csv string of drug names to be imported} ';

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
    public function __construct(OpenFDA $openfda, RXNorm $rxnorm, RXClass $rxclass, MedicalTranslator $translator)
    {
        parent::__construct();

        $this->openfda = $openfda;
        $this->rxnorm = $rxnorm;
        $this->rxclass = $rxclass;
        $this->translator = $translator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Model::unguard(); // allow mass fills

        if ($this->option('verbose')) {
            $this->openfda->setVerbose(true);
            $this->rxnorm->setVerbose(true);
            $this->rxnorm->setVerbose(true);
        }

        $this->limit = (int)$this->option('limit');
        $this->skip = (int)$this->option('skip');

        $this->drugs = Drug::lists('id', 'rxcui');
        $this->indications = DrugIndication::lists('id', 'value');
        $this->side_effects = DrugSideEffect::lists('id', 'value');

        $this->importBrands();
    }

    private function importBrands()
    {
        $this->info('Starting drug import... ');

        $brands = $this->getBrands();

        $total = ($this->limit ? : count($brands) - $this->skip);
        $this->info("Importing {$total} brands" . ($this->skip ? ' skipping ' . $this->skip : '') . ' ...');

        $this->importConcepts($brands);

        $total = count($this->concepts);
        $this->info("Imported {$total} concepts. Importing alternatives ...");

        $this->importAlternatives();

        $this->info('Drug Import Done!');
    }

    private function importConcepts($brands)
    {
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
    }

    private function importAlternatives()
    {
        $imported = 0;

        foreach ($this->concepts as $rxcui => $concept) {
            $imported++;
            $alternatives = [];

            foreach ($concept['related'] as $related) {
                if ($related != $rxcui) {
                    if (!empty($this->concepts[$related])) {
                        $alternatives[] = $this->concepts[$related]['drug']->id;
                    } elseif ($this->drugs->get($related)) {
                        $alternatives[] = $this->drugs->get($related);
                    }
                }
            }

            $drug = $concept['drug'];

            if ($this->option('debug')) {
                $this->comment("Alternatives for {$drug->id} ({$drug->rxcui}) [{$drug->label}] : " . join(',', $alternatives));
            } else {
                $drug->alternatives()->sync($alternatives);
            }

            if (!$this->option('debug') && $imported % 100 == 0) {
                $this->line("Imported {$imported} (out of {$total}) alternatives so far ...");
            }
        }
    }

    private function getBrandsFromNames($names)
    {
        $brands = [];

        foreach ($names as $name) {
            if ($name = trim($name)) {
                $concept = $this->rxnorm->getConceptFromName($name);
                if (empty($concept) || !in_array($concept['tty'], ['BN', 'BPCK'])) {
                    $this->info($name . ' not found or not a brand drug. Skipping ...');
                } else {
                    $brands[] = ['rxcui' => $concept['rxcui']];
                }
            }
        }

        return $brands;
    }

    private function getBrandsFromIds($ids)
    {
        $brands = [];

        foreach ($ids as $id) {
            if ($id = trim($id)) {
                $concept = $this->rxnorm->getConceptProperties($id);
                if (empty($concept) || !in_array($concept['tty'], ['BN', 'BPCK'])) {
                    $this->info($id . ' not found or not a brand drug. Skipping ...');
                } else {
                    $brands[] = ['rxcui' => $id];
                }
            }
        }

        return $brands;
    }

    private function getBrands()
    {
        $brands = [];

        if ($names = str_getcsv($this->option('names'))) {
            $brands = array_merge($brands, $this->getBrandsFromNames($names));
        }

        if ($ids = str_getcsv($this->option('ids'))) {
            $brands = array_merge($brands, $this->getBrandsFromIds($ids));
        }

        if (empty($brands)) {
            $brands = $this->rxnorm->getAllBrands();
        }

        return $brands;
    }

    private function importConcept($rxcui)
    {
        if ($concept = $this->getConceptValues($rxcui)) {
            $drug = Drug::firstOrNew(['rxcui' => $rxcui]);

            $drug->label = $concept['label'];
            $drug->generic = $concept['generic'];
            $drug->drug_forms = $concept['drug_forms'];
            $drug->description = $concept['description'];
            $drug->recalls = $concept['recalls'];

            if ($this->option('debug')) {
                if (!$drug->id) {
                    $drug->id = count($this->drugs) + 1;
                }
                $this->printDrugInfo($drug, $concept);
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
            $this->drugs->put($rxcui, $drug->id);
        }
    }

    private function printDrugInfo($drug, $concept)
    {
        $this->comment("\n==============================================================================");
        $this->comment("Importing rxcui: [{$drug->rxcui}] as drug: [{$drug->id}] label: [{$drug->label} ({$drug->generic})]");
        $this->comment("==============================================================================\n");

        $print = $drug->toArray();

        foreach ($print as $field => $value) {
            if (!is_array($value)) {
                $this->line(sprintf('  %-20s : %s', substr($field, 0, 20), $value));
            }
        }

        foreach (['drug_forms', 'types', 'indications', 'side_effects', 'related'] as $field) {
            $this->line(sprintf('  %-20s : [%s]', $field, join(', ', $concept[$field])));
        }

        $this->line(sprintf('  %-20s : %s', 'recalls', json_encode($concept['recalls'], JSON_PRETTY_PRINT)));
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
        $terms = $this->openfda->getIndications($drug_components);
        return $this->processRelations($terms, $this->indications, 'DrugIndication');
    }

    private function getSideEffects($drug_components)
    {
        $terms = $this->openfda->getSideEffects($drug_components);
        return $this->processRelations($terms, $this->side_effects, 'DrugSideEffect');
    }

    private function processRelations($terms, $model, $provider)
    {
        $relations = [];

        foreach ($terms as $term => $count) {
            if (count($relations) >= 20 || $count < 5) {
                break;
            }

            if ($relation = $this->translator->translate($term)) {
                $id = $model->get($relation);
                if (empty($id)) {
                    if ($this->option('debug')) {
                        $id = $model->max() + 1;
                        $this->comment("New $provider object: id [{$id}] value [{$relation}]");
                    } else {
                        $id = call_user_func(['\App\Facades\\' . $provider, 'create'], ['value' => $relation])->id;
                    }
                    $model->put($relation, $id);
                }

                $relations[] = $id;
            }
        }
        return array_unique($relations);
    }
}
