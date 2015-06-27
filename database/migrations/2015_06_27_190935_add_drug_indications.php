<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDrugIndications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drugs', function ($table) {
            $table->dropColumn('indications');
        });

        //Picklist
        Schema::create('drug_indications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
        });

        //Many to Many relation table
        Schema::create('drug_drug_indication', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('drug_indication_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('drug_indications');
        Schema::drop('drug_drug_indication');
        Schema::table('drugs', function (Blueprint $table) {
            $table->json('indications')->nullable();
        });
    }
}
