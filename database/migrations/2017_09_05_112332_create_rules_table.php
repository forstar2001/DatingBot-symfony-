<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //rules
        Schema::create('rules', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->integer('order')->unsigned();
            $table->integer('scenario_id')->unsigned();
            $table->foreign('scenario_id')->references('id')->on('scenarios');
            $table->unique(['name', 'scenario_id']);
        });

        //languages
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->string('code', 20);
            $table->unique(['name']);
        });

        //language for dialogs
        Schema::table('dialogs', function (Blueprint $table) {
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages');
        });

        DB::table('languages')->insert(
            ['name' => 'English (United States)', 'code' => 'ENUS'],
            ['name' => 'English (Great Britain)', 'code' => 'ENGB'],
            ['name' => 'Swedish', 'code' => 'SE'],
            ['name' => 'Danish', 'code' => 'DE'],
            ['name' => 'Deustch', 'code' => 'GE'],
            ['name' => 'Belgian', 'code' => 'BE'],
            ['name' => 'Portugal', 'code' => 'POR'],
            ['name' => 'French', 'code' => 'FR'],
            ['name' => 'Russian', 'code' => 'RUS'],
            ['name' => 'Chinese', 'code' => 'CH'],
            ['name' => 'Thai', 'code' => 'TH']
        );

        //condition types
        Schema::create('condition_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->unique(['name']);
        });

        DB::table('condition_types')->insert(
            ['name' => 'if'],
            ['name' => 'else if'],
            ['name' => 'else']
        );

        //message templates
        Schema::create('message_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('variable_name', 100);
            $table->unique('variable_name');
        });

        //message templates content
        Schema::create('message_template_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('message_template_id')->unsigned();
            $table->foreign('message_template_id')->references('id')->on('message_templates');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages');
            $table->string('text', 10000);
            $table->unique(['message_template_id', 'language_id']);
        });

        //conditions
        Schema::create('conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order')->unsigned();
            $table->integer('condition_type_id')->unsigned();
            $table->foreign('condition_type_id')->references('id')->on('condition_types');
            $table->integer('rule_id')->unsigned();
            $table->foreign('rule_id')->references('id')->on('rules');
            $table->text('condition')->nullable();
            $table->text('result_message');
            $table->integer('timing_min')->unsigned()->nullable;
            $table->integer('timing_max')->unsigned();
            $table->unique(['order', 'rule_id']);
        });

        //non-response conditions
        Schema::create('nonresponse_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('order')->unsigned();
            $table->integer('nonresponse_time')->unsigned();
            $table->integer('rule_id')->unsigned();
            $table->foreign('rule_id')->references('id')->on('rules');
            $table->integer('timing_min')->unsigned()->nullable;
            $table->integer('timing_max')->unsigned();
            $table->string('result_message', 10000);
            $table->unique(['order', 'rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nonresponse_conditions');
        Schema::dropIfExists('conditions');
        Schema::dropIfExists('message_template_contents');
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('condition_types');
        Schema::table('dialogs', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropColumn('language_id');
        });
        Schema::dropIfExists('languages');
        Schema::dropIfExists('rules');
    }
}
