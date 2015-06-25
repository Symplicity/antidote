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
    public function run() {
        if (Schema::hasTable('concepts')) {
            /* TODO: fetch concepts data from API in this seeder - currently using db table until that is ready */
            $concepts = DB::select('select * from concepts');

            foreach ($concepts as $concept) {
                $data = json_decode($concept['data'], true);

                Drug::create([
                    'label' => ucfirst($data['name']),
                    'rxcui' => $data['rxcui'],
                    'generic' => ucfirst($data['generic']),
                    'drug_forms' => $data['drug_forms']
                ]);
            }

            //add many-to-many relations
            $drugs = DB::select('select rxcui, id from drugs');
            $drug_map = array_column($drugs, 'id', 'rxcui');

            foreach ($concepts as $concept) {
                $data = json_decode($concept['data'], true);
                $drug = Drug::where('rxcui', '=', $concept['rxcui'])->first();

                if ($data['related'] && is_array($data['related'])) {
                    $related = $data['related'];
                    $rels = [];
                    foreach ($related as $related_drug_rxcui) {
                        if (isset($drug_map[$related_drug_rxcui])) {
                            $rels[] = $drug_map[$related_drug_rxcui];
                        }
                    }
                    $drug->related()->sync($rels);
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
