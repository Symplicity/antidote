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
        Schema::table('drugs', function (Blueprint $table) {
            $table->string('generic_id')->nullable();
            $table->json('indications')->nullable();
            $table->string('prescription_type')->nullable();
            $table->json('recalls')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drugs', function (Blueprint $table) {
            $table->dropColumn('generic_id');
            $table->dropColumn('indications');
            $table->dropColumn('prescription_type');
            $table->dropColumn('recalls');
        });
    }
}
