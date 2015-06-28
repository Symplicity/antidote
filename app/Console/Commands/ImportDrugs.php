<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Drug;
use App\DrugSideEffect;
use App\DrugIndication;

class ImportDrugs extends Command
{
    private $side_effects_map;
    private $indications_map;

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
    protected $description = 'Import drug data from various APIs such as OpenFDA.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        Config::set('database.fetch', \PDO::FETCH_ASSOC);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting drug import...');

        if (Schema::hasTable('concepts')) {
            /* TODO: fetch concepts data from API in this seeder - currently using db table until that is ready */
            $concepts = DB::select('select * from concepts');

            foreach ($concepts as $concept) {
                $data = json_decode($concept['data'], true);

                Drug::create([
                    'label' => ucfirst($data['label']),
                    'rxcui' => $concept['rxcui'],
                    'generic' => ucfirst($data['generic']),
                    'drug_forms' => $data['dose_forms'],
                    'generic_id' => isset($data['ingredient']) ? $data['ingredient'] : null,
                    'indications' => $data['indications'],
                    'prescription_type' => $data['prescription_type'],
                    'recalls' => $data['recalls'],
                    'description' => $data['description']
                ]);
            }

            //add many-to-many relations
            $drugs = DB::select('select rxcui, id from drugs');
            $drug_map = array_column($drugs, 'id', 'rxcui');

            $all_side_effects = DB::select('select value, id from drug_side_effects');
            $this->side_effects_map = array_column($all_side_effects, 'id', 'value');

            $all_indications = DB::select('select value, id from drug_indications');
            $this->indications_map = array_column($all_indications, 'id', 'value');

            foreach ($concepts as $concept) {
                $data = json_decode($concept['data'], true);
                $drug = Drug::where('rxcui', '=', $concept['rxcui'])->first();

                if (!empty($data['related']) && is_array($data['related'])) {
                    $related = $data['related'];
                    $rels = [];
                    foreach ($related as $related_drug_rxcui) {
                        if (isset($drug_map[$related_drug_rxcui])) {
                            $rels[] = $drug_map[$related_drug_rxcui];
                        }
                    }
                    $drug->related()->sync($rels);
                }

                if (!empty($data['alternatives']) && is_array($data['alternatives'])) {
                    $alternatives = $data['alternatives'];
                    $alts = [];
                    foreach ($alternatives as $alternative_drug_rxcui) {
                        if (isset($drug_map[$alternative_drug_rxcui])) {
                            $alts[] = $drug_map[$alternative_drug_rxcui];
                        }
                    }
                    $drug->alternatives()->sync($alts);
                }

                $side_effects = $this->extractPicks('side_effects', $data);
                $indications = $this->extractPicks('indications', $data);

                if (!empty($side_effects)) {
                    $drug->sideEffects()->sync($side_effects);
                }

                if (!empty($indications)) {
                    $drug->indications()->sync($indications);
                }
            }
        }

        $this->info('Drug import finished!');
    }

    private function extractPicks($field, $data)
    {
        $picks = [];

        if (!empty($data[$field]) && is_array($data[$field])) {
            $raw_picks = $data[$field];
            foreach ($raw_picks as $raw_pick) {
                $val = trim(strtolower($raw_pick));
                switch ($field) {
                    case 'side_effects':
                        if (isset($this->side_effects_map[$val])) {
                            $picks[] = $this->side_effects_map[$val];
                        } else {
                            $pick = DrugSideEffect::create([
                                'value' => $val
                            ]);
                            $this->side_effects_map[$pick->value] = $pick->id;
                            $picks[] = $pick->id;
                        }
                        break;
                    case 'indications':
                        if (isset($this->indications_map[$val])) {
                            $picks[] = $this->indications_map[$val];
                        } else {
                            $pick = DrugIndication::create([
                                'value' => $val
                            ]);
                            $this->indications_map[$pick->value] = $pick->id;
                            $picks[] = $pick->id;
                        }
                        break;
                }
            }
        }
        return $picks;
    }
}
