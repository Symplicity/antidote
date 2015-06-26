<?php

use Illuminate\Database\Seeder;
use App\Drug;

class DrugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('concepts')) {
            /* TODO: fetch concepts data from API in this seeder - currently using db table until that is ready */
            $concepts = DB::select('select * from concepts');

            foreach ($concepts as $concept) {
                $data = json_decode($concept['data'], true);

                Drug::create([
                    'label' => ucfirst($data['name']),
                    'rxcui' => $data['rxcui'],
                    'generic' => ucfirst($data['generic']),
                    'drug_forms' => $data['drug_forms'],
                    'generic_id' => $data['ingredient'],
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
                        }
                    }
                    $drug->sideEffects()->sync($picks);
                }
            }
        } else {
            //fake data
            factory('App\Drug', 50)->create()->each(function ($drug) {
                $drug->sideEffects()->sync([1, 2, 3]);

                $drug->alternatives()->sync([1, 2, 3]);

                $drug->related()->sync([1, 2, 3]);
            });
        }
    }
}
