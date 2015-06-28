<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //test user
        factory('App\User')->create([
            'username' => 'test'
        ]);
        factory('App\User', 50)->create();
    }
}
