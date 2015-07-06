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

        $drug_import_args = [];
        if (getenv('APP_ENV') == 'local') {
            $drug_import_args = [
                '--limit' => 50
            ];
        }

        Artisan::call('import:drugs', $drug_import_args);

        //drugs are now seeded above -
        //for dev it will seed 50 drugs and for prod it will seed all drugs
        $this->call('UserSeeder');
        $this->call('DrugReviewSeeder');
    }
}
