<?php

use Illuminate\Database\Seeder;

class PicklistSeeder extends Seeder
{
    public function run()
    {
        DB::table('drug_side_effects')->insert(
            [
                [
                    'value' => 'Nausea'
                ],
                [
                    'value' => 'Diarrhea'
                ],
                [
                    'value' => 'Constipation'
                ]
            ]
        );

        // Insert values for drug ratings picklist
        DB::table('drug_ratings')->insert(
            [
                [
                    'value' => 'It Worked!'
                ],
                [
                    'value' => 'Not So Great'
                ],
                [
                    'value' => 'Didnt work, used another medication'
                ]
            ]
        );
    }
}
