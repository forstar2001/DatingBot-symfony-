<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCreateCountryCityRegions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('cities');
        Schema::drop('countries');

        Schema::create('countries', function(Blueprint $table) {
           $table->increments('id');
           $table->timestamps();
           $table->string('name', 255);
           $table->unique('name');
           $table->string('code', 10)->nullable();
           $table->unique('code');
           $table->string('additional_code', 10)->nullable();
           $table->unique('additional_code');
        });

        Schema::create('regions', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 255);
            $table->unique('name');
            $table->string('code', 10)->nullable();
            $table->unique('code');
            $table->string('additional_code', 10)->nullable();
            $table->unique('additional_code');
            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');
        });

        Schema::create('cities', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 255);
            $table->unique('name');
            $table->string('code', 10)->nullable();
            $table->unique('code');
            $table->string('additional_code', 10)->nullable();
            $table->unique('additional_code');
            $table->integer('region_id')->unsigned();
            $table->foreign('region_id')->references('id')->on('regions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
