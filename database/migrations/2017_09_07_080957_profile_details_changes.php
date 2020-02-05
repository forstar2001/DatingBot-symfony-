<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfileDetailsChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_detail_types', function (Blueprint $table) {
            $table->string('reference_table_column', 100)->nullable();
            $table->integer('reference_id')->nullable()->unsigned();
        });

        //countries
        Schema::create('countries', function (Blueprint $table) {
           $table->increments('id');
           $table->timestamps();
           $table->string('name', 171);
           $table->unique(['name']);
        });

        //cities
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 171);
            $table->unique(['name']);
            $table->integer('country_id')->unsigned();
            $table->foreign('country_id', 'city_country_id_foreign')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');

        Schema::table('profile_detail_types', function (Blueprint $table) {
            $table->dropColumn(['reference_table_column', 'reference_id']);
        });
    }
}
