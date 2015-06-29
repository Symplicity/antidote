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
        factory('App\DrugReview', 500)->create()->each(function ($drug_review) {
            $drug_review->sideEffects()->sync([1, 2, 3]);

            for ($i = 0; $i < 50; $i++) {
                $drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => $i]));
            }
        });
    }
}
