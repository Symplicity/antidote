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

        $review_limit = 5000;
        if (getenv('APP_ENV') == 'local') {
            $review_limit = 500;
        }

        factory('App\DrugReview', $review_limit)->create()->each(function ($drug_review) use ($faker, $ids) {
            $side_effects = [];
            if ($side_effect_objects = $drug_review->drug->sideEffects()->get()->toArray()) {
                if (count($side_effect_objects)) {
                    foreach ($side_effect_objects as $se) {
                        $side_effects[] = $se['id'];
                    }
                }
            }

            if (count($side_effects)) {
                $drug_review->sideEffects()->sync($faker->randomElements($side_effects, $faker->numberBetween(0, 10)));
            }

            $users = $faker->randomElements($ids, $faker->numberBetween(0, 25));
            foreach ($users as $user) {
                $drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => $user]));
            }
        });
    }
}
