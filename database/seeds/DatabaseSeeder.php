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

        Config::set('database.fetch', PDO::FETCH_ASSOC);

        $this->call('PicklistSeeder');
        $this->call('DrugSeeder');
        $this->call('UserSeeder');
        $this->call('DrugReviewSeeder');
    }
}
