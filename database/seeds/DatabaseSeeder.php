<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        if (getenv('APP_ENV') == 'local') {
            //for dev its all fake data
            $this->call('UserSeeder');
            $this->call('DrugSideEffectSeeder');
            $this->call('DrugIndicationSeeder');
            $this->call('DrugSeeder');
            $this->call('DrugReviewSeeder');
        } else {
            Artisan::call('import:drugs');

            //drugs are now seeded above -
            //for dev it will seed 50 drugs and for prod it will seed all drugs
            $this->call('UserSeeder');
            $this->call('DrugReviewSeeder');
        }
    }
}
