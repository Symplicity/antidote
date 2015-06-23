<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        $this->call('PicklistSeeder');
        $this->call('DrugSeeder');
        $this->call('UserSeeder');
        $this->call('DrugReviewSeeder');
    }
}
