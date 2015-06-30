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
        
        factory('App\DrugReview')->create()->each(function ($drug_review) use ($faker, $ids) {
            $drug_review->sideEffects()->sync($faker->randomElements($ids, $faker->numberBetween(0, 10)));

            $users = $faker->randomElements($ids, $faker->numberBetween(0, 25));
            foreach ($users as $user) {
                $drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => $user]));
            }
        });
    }
}
