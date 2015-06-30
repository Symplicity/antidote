<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Services\OpenFDA;
use App\Services\RXNorm;
use App\Services\RXClass;

class ImportDrugs extends Command
{
    private $openfda;
    private $rxnorm;
    private $rxdclass;

    private $concepts = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:drugs';

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
        $this->info('Starting drug import...');

        $this->drugs_map = App\Drug::lists('id', 'rxcui');

        $this->importConcepts();
        $this->importConceptRelations();
    }

    private function getConcept($rxcui)
    {
        $concept = ['rxcui' => $rxcui, 'type' => 'generic'];

        $properties = $this->rxnorm->getProperties($rxcui);
        if (!empty($properties)) {
            $concept['label'] = $this->rxnorm->getLabel($properties);
            $concept['sanitized_brand'] = $this->openfda->sanitizeName($concept['label']);
            $concept['generic'] = $concept['label'];
            $concept['sanitized_generic'] = $concept['sanitized_brand'];
            $concept['relations'] = $this->rxnorm->getRelations($properties);
        }
    }

    private function importConcept($concept)
    {
        $rxcui = $concept['rxcui'];
        $ingredients = $concept['relations']['ingredients'];

        $drug = App\Drug::firstOrNew(['rxcui' => $rxcui]);

        $drug->type = array_get($concept, 'type', 'brand');
        $drug->label = ucwords(array_get($concept, 'label', ''));
        $drug->generic = ucwords(array_get($concept, 'generic', ''));
        $drug->generic_id = array_get($concept, 'ingredient', null);
        $drug->drug_forms = array_get($concept, 'dose_forms', null);

        $labels = $this->getLabels($concept);
        $drug->description = $this->getDescription($concept, $labels);
 
        $prescription_types = $this->getPrescriptionTypes($concept, $labels);
        $drug->prescriptionTypes()->sync($prescription_types);

        $drug->recalls = $this->getRecalls($concept);
        $drug->save();

        $concept['drug'] = $drug;
        $concept['alternatives'] = $this->getAlternatives($concept);
        $concept['side_effects'] = $this->getSideEffects($concept);
        $concept['indications'] = $this->getIndications($concept);

        return $concept;
    }

    private function importConcepts()
    {
        $counts = ['brands' => 0, 'generics' => 0];

        $brands = $this->rxnorm->getAllBrands();
        foreach ($brands as $brand) {
            $rxcui = $brand['rxcui'];

            $this->concepts[$rxcui] = $this->getConcept($rxcui);
            $this->concepts[$rxcui]['type'] = 'brand';

            if (empty($this->concepts[$rxcui]['relations']['ingredient'])) {
                $this->concepts[$rxcui]['generic'] = '';
                $this->concepts[$rxcui]['sanitized_generic'] = '';
            } else {
                $irxcui = $this->concepts[$rxcui]['relations']['ingredient'];

                if (empty($this->concepts[$irxcui])) {
                    $this->concepts[$irxcui] = $this->getConcept($irxcui);
                    $this->concepts[$irxcui] = $this->importConcept($this->concepts[$irxcui]);
                    $counts['generics']++;
                }

                $this->concepts[$rxcui]['generic'] = $this->concepts[$irxcui]['label'];
                $this->concepts[$rxcui]['sanitized_generic'] = $this->concepts[$irxcui]['sanitized_generic'];
            }

            $this->concepts[$rxcui] = $this->importConcept($this->concepts[$rxcui]);
            $counts['brands']++;
        }
    }


    private function importConceptRelations()
    {
        foreach ($this->concepts as $rxcui => $concept) {
            $related = [];
            foreach ($concept['related'] as $rel) {
                if (!empty($this->concepts[$rel]['drug'])) {
                    $related[] = $this->concepts[$rel]['drug']->id;
                }
            }
            $drug->related()->sync($related);

            $alternatives = [];
            foreach ($concept['alternatives'] as $alternative) {
                if (!empty($this->concepts[$alternative]['drug'])) {
                    $alternaties[] = $this->concepts[$alternative]['drug']->id;
                }
            }
            $drug->alternatives()->sync($alternatives);


            $indications_map = App\DrugIndication::lists('id', 'value');

            $indications = [];
            foreach ($concept['indications'] as $indication) {
                if (!empty($indications_map[$indication])) {
                    $indications[] = $indications_map[$indication];
                } else {
                    $indications[] = App\DrugIndication::create(['value' => $indication])->id;
                }
            }
            if (!empty($indications)) {
                $drug->indications()->attach($indications);
            }


            $side_effect_map = App\DrugIndication::lists('id', 'value');

            $side_effect = [];
            foreach ($concept['side_effects'] as $side_effect) {
                if (!empty($side_effects_map[$side_effect])) {
                    $side_effects[] = $side_effect_map[$side_effect];
                } else {
                    $side_effects[] = App\DrugSideEffect::create(['value' => $side_effect])->id;
                }
            }
            if (!empty($side_effects)) {
                $drug->sideEffects()->attach($side_effects);
            }
        }
    }

    private function getLabels($concept)
    {
        $brand = $concept['sanitized_brand'];
        $generic = $concept['sanitized_generic'];

        $labels = $this->openfda->getDrugLabels($brand, $generic);

        return $labels;
    }

    private function getPrescriptionTypes($concept, $labels)
    {
        $types = $this->openfda->getPrescriptionTypes($labels);

        return $types;
    }

    private function getDescription($concept, $labels)
    {
        $type = $concept['type'];
        if ($type == 'brand') {
            $description = $this->openfda->getDescription('brand', $concept['sanitized_brand'], $labels);
        } else {
            $description = $this->openfda->getDescription('generic', $concept['sanitized_generic'], $labels);
        }

        return $description;
    }

    private function getIndications($concept)
    {
        $indications = [];

        $ingredients = $concept['relations']['ingredients'];
        if (!empty($ingredients)) {
            foreach ($ingredients as $ingredient) {
                $indications = array_merge($indications, $this->rxclass->getIndications($ingredient));
            }
        }

        if (empty($indications)) {
            $brand = $concept['sanitized_brand'];
            $generic = $concept['sanitized_generic'];

            $indications = $this->openfda->getIndications($brand, $generic);
        }

        return array_unique($indications);
    }

    private function getSideEffects($concept)
    {
        $brand = $concept['sanitized_brand'];
        $generic = $concept['sanitized_generic'];

        $side_effects = $this->openfda->getSideEffects($brand, $generic);

        return array_unique($side_effects);
    }

    private function getAlternatives($concept)
    {
        $alternatives = [];

        $ingredients = $concept['relations']['ingredients'];
        if (!empty($ingredients)) {
            foreach ($ingredients as $ingredient) {
                $members = $this->rxclass->getRelated($ingredient);
                foreach ($members as $member) {
                    $alternatives = array_merge($alternatives, $this->rxnorm->getRelated($member, 'tty=BN+BPCK'));
                }
            }
        }

        return array_unique($alternatives);
    }

    private function getRecalls($concept)
    {
        return $this->openfda->getRecalls($concept['rxcui']);
    }
}
