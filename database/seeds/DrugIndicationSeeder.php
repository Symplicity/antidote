<?php

use Illuminate\Database\Seeder;

class DrugIndicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\DrugIndication', 50)->create();
    }
}
