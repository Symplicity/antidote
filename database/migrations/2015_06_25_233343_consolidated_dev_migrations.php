<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConsolidatedDevMigrations extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->default('');
            $table->string('email')->default('');
            $table->string('password', 60);
            $table->string('reset_password_token')->nullable();
            $table->dateTime('reset_password_token_expiration')->nullable();
            $table->date('age')->nullable();
            $table->char('gender', 1)->nullable();
            $table->timestamps();
        });

        Schema::create('drugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->default('');
            $table->string('description')->nullable();
            $table->integer('rxcui')->default(0);
            $table->text('generic')->nullable();
            $table->json('drug_forms')->default('');
            $table->timestamps();
        });

        Schema::create('drug_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('drug_id')->unsigned();
            $table->integer('rating')->unsigned();
            $table->boolean('is_covered_by_insurance');
            $table->integer('age')->nullable()->unsigned();
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

    public function down()
    {
        Schema::drop('users');
        Schema::drop('drugs');
        Schema::drop('drug_reviews');
        Schema::drop('drug_side_effects');
        Schema::drop('drug_drug_side_effect');
        Schema::drop('drug_review_drug_side_effect');
        Schema::drop('drug_ratings');
        Schema::drop('drug_alternative_drugs');
        Schema::drop('drug_related_drugs');
    }
}
