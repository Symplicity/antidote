<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDrugFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepts', function ($table) {
            $table->integer('rxcui')->unsigned();
            $table->text('data');
        });

        Schema::table('drugs', function (Blueprint $table) {
            $table->integer('rxcui');
            $table->text('generic')->nullable();
            $table->json('drug_forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drugs', function ($table) {
            $table->dropColumn('rxcui');
            $table->dropColumn('generic');
            $table->dropColumn('drug_forms');
        });

        Schema::drop('concepts');
    }
}
