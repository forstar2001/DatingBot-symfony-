<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourcesBotProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources_bot_profiles', function (Blueprint $table){
            $table->increments('id');
            $table->integer('source_id')->unsigned();
            $table->integer('bot_profile_id')->unsigned();
            $table->foreign(['source_id'])->references('id')->on('sources');
            $table->foreign(['bot_profile_id'])->references('id')->on('bot_profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sources_bot_profiles');
    }
}
