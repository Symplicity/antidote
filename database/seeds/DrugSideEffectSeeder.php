<?php

use Illuminate\Database\Seeder;

class DrugSideEffectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\DrugSideEffect', 50)->create();
    }
}
