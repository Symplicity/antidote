<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Drug;

class ImportDrugs extends Command
{
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
            $side_effect_map = array_column($all_side_effects, 'id', 'value');

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

                if (!empty($data['side_effects']) && is_array($data['side_effects'])) {
                    $side_effects = $data['side_effects'];
                    $picks = [];
                    foreach ($side_effects as $side_effect) {
                        if (isset($side_effect_map[trim(strtolower($side_effect))])) {
                            $picks[] = $side_effect_map[trim(strtolower($side_effect))];
                        } else {
                            //TODO: create new pick and get ID and add to $picks array
                        }
                    }
                    $drug->sideEffects()->sync($picks);
                }
            }
        }

        $this->info('Drug import finished!');
    }
}
