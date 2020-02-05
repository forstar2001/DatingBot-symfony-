<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPersonProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('person_profiles', function (Blueprint $table) {
            $table->integer('country_id')->unsigned();
            $table->integer('region_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')
                ->on('countries')
                ->onDelete('cascade');
            $table->foreign('region_id')->references('id')
                ->on('regions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('person_profiles', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['region_id']);
            $table->dropColumn('country_id');
            $table->dropColumn('region_id');
        });
    }
}
