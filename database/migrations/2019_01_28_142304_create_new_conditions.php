<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditions_alltime', function(Blueprint $table) {
           $table->increments('id');
           $table->integer('order')->unsigned();
           $table->string('condition');
           $table->string('result_message');
           $table->integer('timing_min')->unsigned();
           $table->integer('timing_max')->unsigned();
        });

        Schema::create('conditions_nosence', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('order')->unsigned();
            $table->string('condition');
            $table->string('result_message');
            $table->integer('timing_min')->unsigned();
            $table->integer('timing_max')->unsigned();
        });

        Schema::create('nonresponse_conditions_alltime', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('order')->unsigned();
            $table->integer('nonresponse_time')->unsigned();
            $table->string('result_message');
            $table->integer('timing_min')->unsigned();
            $table->integer('timing_max')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conditions_alltime');
        Schema::dropIfExists('nonresponse_conditions_alltime');
        Schema::dropIfExists('conditions_nosence');
    }
}
