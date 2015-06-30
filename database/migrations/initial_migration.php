<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConsolidatedDevMigrations extends Migration
{
    public function up()
    {
        Schema::create('drug_prescription_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
        });

        // Fill prescription types table
        DB::table('drug_prescription_types')->insert([
            ['value' => 'Prescription'],
            ['value' => 'Over The Counter'],
        ]);

        Schema::create('drug_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
        });

        // Fill drug ratings table
        DB::table('drug_ratings')->insert([
            ['value' => 'Bad'],
            ['value' => 'Good'],
            ['value' => 'Best']
        ]);

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

            $table->index('username');
        });

        Schema::create('drugs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rxcui')->default(0);
            $table->string('type')->default('brand');
            $table->string('label')->default('');
            $table->text('generic')->default('');
            $table->integer('generic_id')->nullable();
            $table->string('description')->nullable();
            $table->json('drug_forms')->nullable();
            $table->json('recalls')->nullable();
            $table->timestamps();

            $table->index('rxcui');
            $table->index('type');
            $table->foreign('generic_id')->references('id')->on('drugs');
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

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->foreign('rating')->referenes('id')->on('drug_ratings')->onDelete('cascade');

            $table->index(['is_covered_by_insurance']);
            $table->index(['age']);
        });

        Schema::create('drug_review_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('drug_review_id')->unsigned();
            $table->integer('vote');
            $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('drug_id')->references('id')->on('drug_reviews')->onDelete('cascade');

            $table->index(['vote']);
        });

        Schema::create('drug_side_effects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
            $table->timestamps();
        });

        Schema::create('drug_indications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
            $table->timestamps();
        });

       //Many to Many relation table
        Schema::create('drug_related', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('related_id')->unsigned();

            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->foreign('related_id')->references('id')->on('drugs');

            $table->primary(['drug_id', 'related_id']);
        });

        //Many to Many relation table
        Schema::create('drug_alaternatives', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('alternative_id')->unsigned();

            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('drugs');

            $table->primary(['drug_id', 'alternative_id']);
        });

        //Many to Many relation table
        Schema::create('drug_drug_prescription_type', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('drug_prescription_type_id')->unsigned();

            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->foreign('drug_prescription_type_id')->references('id')->on('drug_prescription_types');

            $table->primary(['drug_id', 'drug_prescriptiont_type_id']);
        });

        //Many to Many relation table
        Schema::create('drug_drug_indication', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('drug_indication_id')->unsigned();

            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->foreign('drug_indication_id')->references('id')->on('drug_indications');

            $table->primary(['drug_id', 'drug_indication_id']);
        });

        //Many to Many relation table
        Schema::create('drug_drug_side_effect', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned();
            $table->integer('drug_side_effect_id')->unsigned();

            $table->foreign('drug_id')->references('id')->on('drugs');
            $table->foreign('drug_side_effect_id')->references('id')->on('drug_side_effects');

            $table->primary(['drug_id', 'drug_side_effect_id']);
        });
 
        //Many to Many relation table
        Schema::create('drug_review_drug_side_effect', function (Blueprint $table) {
            $table->integer('drug_review_id')->unsigned();
            $table->integer('drug_side_effect_id')->unsigned();

            $table->foreign('drug_review_id')->references('id')->on('drug_reviews');
            $table->foreign('drug_side_effect_id')->references['id']->on('drug_side_effects');

            $table->primary(['drug_review_id', 'drug_side_effect_id']);
        });
    }

    public function down()
    {
        Schema::drop('users');
        Schema::drop('drugs');
        Schema::drop('drug_reviews');
        Schema::drop('drug_side_effects');
        Schema::drop('drug_indications');
        Schema::drop('drug_prescription_types');
        Schema::drop('drug_ratings');
        Schema::drop('drug_related_drugs');
        Schema::drop('drug_alternative_drugs');
        Schema::drop('drug_drug_rating');
        Schema::drop('drug_drug_prescription_type');
        Schema::drop('drug_drug_indication');
        Schema::drop('drug_drug_side_effect');
        Schema::drop('drug_review_drug_side_effect');
        Schema::drop('drug_review_votes');
    }
}
