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

        factory('App\DrugSideEffect', 50)->create();

        factory('App\Drug', 50)->create()->each(function ($drug) use ($faker) {
            $drug->sideEffects()->sync([$faker->numberBetween(1, 50), $faker->numberBetween(1, 50), $faker->numberBetween(1, 50)]);

            $drug->alternatives()->sync([$faker->numberBetween(1, 50), $faker->numberBetween(1, 50), $faker->numberBetween(1, 50)]);

            $drug->related()->sync([$faker->numberBetween(1, 50), $faker->numberBetween(1, 50), $faker->numberBetween(1, 50)]);
        });
    }
}
