<?php

use Illuminate\Database\Seeder;

class DrugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Drug', 50)->create()->each(function ($drug) {
            $drug->sideEffects()->sync([1, 2, 3]);

            $drug->alternatives()->sync([1, 2, 3]);

            $drug->related()->sync([1, 2, 3]);
        });
    }
}
