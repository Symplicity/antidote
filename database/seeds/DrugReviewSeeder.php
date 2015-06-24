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
        });
    }
}
