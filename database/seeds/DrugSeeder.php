<?php

use Illuminate\Database\Seeder;
use App\Drug;
use Illuminate\Support\Facades\Config;

class DrugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::set('database.fetch', PDO::FETCH_ASSOC);

        $concepts = DB::select('select * from concepts');

        foreach($concepts as $concept) {
            $data = json_decode($concept['data'], true);

            Drug::create([
                'label' => ucfirst($data['name']),
                'rxcui' => $data['rxcui'],
                'generic' => ucfirst($data['generic']),
                'drug_forms' => json_encode($data['drug_forms'])
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
                foreach($related as $related_drug_rxcui) {
                    if (isset($drug_map[$related_drug_rxcui])) {
                        $rels[] = $drug_map[$related_drug_rxcui];
                    }
                }
                $drug->related()->sync($rels);
            }
        }

        Schema::drop('concepts');
    }
}
