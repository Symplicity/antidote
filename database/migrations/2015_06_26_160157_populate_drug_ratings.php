<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateDrugRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('drug_ratings')->truncate();

        // Insert values for drug ratings picklist
        DB::table('drug_ratings')->insert(
            [
                [
                    'value' => 'It Worked!'
                ],
                [
                    'value' => 'Not So Great'
                ],
                [
                    'value' => 'Didn\'t work, used another medication'
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('drug_ratings')->truncate();
    }
}
