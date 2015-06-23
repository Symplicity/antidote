<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrugReviewsDataModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drug_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('drug_id')->unsigned();
            $table->integer('rating')->unsigned();
            $table->boolean('is_covered_by_insurance');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        //Many to Many relation table
        Schema::create('drug_review_drug_side_effect', function (Blueprint $table) {
            $table->integer('drug_review_id')->unsigned();
            $table->integer('drug_side_effect_id')->unsigned();
        });

        Schema::create('drug_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
        });

        //Picklist
        Schema::create('drug_side_effects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
        });

        //Many to Many relation table
        Schema::create('drug_drug_side_effect', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('drug_side_effect_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('drug_reviews');
        Schema::drop('drug_side_effects');
        Schema::drop('drug_drug_side_effect');
        Schema::drop('drug_review_drug_side_effect');
        Schema::drop('drug_ratings');
    }
}
