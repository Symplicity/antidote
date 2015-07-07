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
        $faker = Faker\Factory::create();
        $ids = range(1, 50);

        factory('App\Drug', 50)->create()->each(function ($drug) use ($faker, $ids) {

            $drug->alternatives()->sync($faker->randomElements($ids, $faker->numberBetween(0, 10)));
            $drug->related()->sync($faker->randomElements($ids, $faker->numberBetween(0, 10)));
            $drug->sideEffects()->sync($faker->randomElements($ids, $faker->numberBetween(0, 10)));
            $drug->indications()->sync($faker->randomElements($ids, $faker->numberBetween(0, 10)));

            $drug->prescriptionTypes()->sync($faker->randomElements([1, 2], $faker->numberBetween(0, 2)));
        });
    }
}
