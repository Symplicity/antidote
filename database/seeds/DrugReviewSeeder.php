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
        factory('App\DrugReview', 500)->create();
    }
}
