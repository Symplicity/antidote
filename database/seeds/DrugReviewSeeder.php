<?php

use Illuminate\Database\Seeder;

class DrugReviewSeeder extends Seeder
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
        
        factory('App\DrugReview', 500)->create()->each(function ($drug_review) {
            $drug_review->sideEffects()->sync($faker->randomElements($ids, $faker->numberBetween(0, 10)));
       });
    }
}
