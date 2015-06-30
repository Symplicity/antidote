<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        $this->call('UserSeeder');
        $this->call('DrugIndicationSeeder');
        $this->call('DrugSideEffectSeeder');
        $this->call('DrugSeeder');
        $this->call('DrugReviewSeeder');
    }
}
