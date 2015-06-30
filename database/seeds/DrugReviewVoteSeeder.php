<?php

use Illuminate\Database\Seeder;

class DrugReviewVoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\DrugReviewVote', 500)->create();
    }
}
