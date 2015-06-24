<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DrugAlternativesRelated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Many to Many relation table
        Schema::create('drug_alternative_drugs', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('alternative_drug_id')->unsigned();
        });

        //Many to Many relation table
        Schema::create('drug_related_drugs', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('related_drug_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('drug_alternative_drugs');
        Schema::drop('drug_related_drugs');
    }
}
