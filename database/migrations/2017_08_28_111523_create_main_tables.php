<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //scenario_types
        Schema::create('scenario_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->unique('name');
        });

        //scenarios
        Schema::create('scenarios', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->string('description', 1000)->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['name']);

            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('scenario_types');
        });


        DB::table('scenario_types')->insert(
            ['name' => 'Respond to message'],
            ['name' => 'Start dialog']
        );

        //dialogs
        Schema::create('dialogs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('scenario_id')->unsigned();
            $table->foreign('scenario_id')->references('id')->on('scenarios');
        });

        //bot profiles
        Schema::create('bot_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
        });

        //person profiles
        Schema::create('person_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
        });

        //messages
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('dialog_id')->unsigned();
            $table->foreign('dialog_id')->references('id')->on('dialogs');
            $table->text('message_text');
        });

        // bots/persons sources
        Schema::create('sources', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->unique('name');
        });

        Schema::table('bot_profiles', function (Blueprint $table) {
            $table->integer('source_id')->unsigned();
            $table->foreign('source_id')->references('id')->on('sources');
            $table->unique(['name', 'source_id']);
        });

        Schema::table('person_profiles', function (Blueprint $table) {
            $table->integer('source_id')->unsigned();
            $table->foreign('source_id')->references('id')->on('sources');
            $table->unique(['name', 'source_id']);
        });

        // Dialog's participants
        Schema::create('dialog_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('dialog_id')->unsigned();
            $table->foreign('dialog_id')->references('id')->on('dialogs');
            $table->integer('bot_profile_id')->unsigned()->nullable();
            $table->foreign('bot_profile_id')->references('id')->on('bot_profiles');
            $table->integer('person_profile_id')->unsigned()->nullable();
            $table->foreign('person_profile_id')->references('id')->on('person_profiles');
            $table->unique(['dialog_id', 'bot_profile_id', 'person_profile_id'], 'dialog_participants_unique');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->integer('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on('dialog_participants');
            $table->integer('receiver_id')->unsigned();
            $table->foreign('receiver_id')->references('id')->on('dialog_participants');
        });

        //profile detail value types
        Schema::create('profile_detail_value_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->unique('name');
        });

        DB::table('profile_detail_value_types')->insert(
            ['name' => 'string'],
            ['name' => 'integer'],
            ['name' => 'datetime']
        );

        //profiles detail types
        Schema::create('profile_detail_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->unique('name');
            $table->string('variable_name', 50);
            $table->unique('variable_name');
            $table->integer('min_value')->nullable();
            $table->integer('max_value')->nullable();
            $table->integer('max_string_length')->nullable()->unsigned();
            $table->integer('profile_detail_value_type_id')->unsigned();
            $table->foreign('profile_detail_value_type_id')->references('id')->on('profile_detail_value_types');
            $table->string('regexp')->nullable();
        });

        //bot_profile_details
        Schema::create('bot_profile_details', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('bot_profile_id')->unsigned();
            $table->foreign('bot_profile_id')->references('id')->on('bot_profiles');
            $table->integer('profile_detail_type_id')->unsigned();
            $table->foreign('profile_detail_type_id')->references('id')->on('profile_detail_types');
            $table->string('value', 1000);
            $table->unique(['bot_profile_id', 'profile_detail_type_id'], 'bot_prof_Id_prof_det_type_id');
        });

        //person_profile_details
        Schema::create('person_profile_details', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('person_profile_id')->unsigned();
            $table->foreign('person_profile_id')->references('id')->on('person_profiles');
            $table->integer('profile_detail_type_id')->unsigned();
            $table->foreign('profile_detail_type_id')->references('id')->on('profile_detail_types');
            $table->string('value', 1000);
            $table->unique(['person_profile_id', 'profile_detail_type_id'], 'pers_prof_id_prof_det_type_id');
        });

        /*Schema::table('messages', function (Blueprint $table) {
            $table->tinyInteger('is_sent')->default(0)->unsigned();
        });*/

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_profile_details');
        Schema::dropIfExists('bot_profile_details');
        Schema::dropIfExists('profile_detail_types');
        Schema::dropIfExists('profile_detail_value_types');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('dialog_participants');
        Schema::dropIfExists('bot_profiles');
        Schema::dropIfExists('person_profiles');
        Schema::dropIfExists('sources');
        Schema::dropIfExists('dialogs');
        Schema::dropIfExists('scenarios');
        Schema::dropIfExists('scenario_types');
    }
}
