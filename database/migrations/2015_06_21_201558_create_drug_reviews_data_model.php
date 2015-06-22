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

        // Insert values for drug ratings picklist
        DB::table('drug_ratings')->insert(
            [
                'id' => '1',
                'value' => 'It Worked!'
            ]
        );

        DB::table('drug_ratings')->insert(
            [
                'id' => '2',
                'value' => 'Not So Great'
            ]
        );

        DB::table('drug_ratings')->insert(
            [
                'id' => '3',
                'value' => 'Didnt work, used another medication'
            ]
        );

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
