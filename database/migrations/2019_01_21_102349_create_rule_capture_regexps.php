<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRuleCaptureRegexps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rule_capture_regexps', function(Blueprint $table) {

           Schema::dropIfExists('rule_capture_regexps');


           $table->increments('id');
           $table->text('regexp');
           $table->integer('profile_detail_type_id')->unsigned();
           $table->foreign('profile_detail_type_id')
               ->references('id')
               ->on('profile_detail_types');
           $table->integer('rule_id')->unsigned();
           $table->foreign('rule_id')
               ->references('id')
               ->on('rules')
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
        Schema::dropIfExists('rule_capture_regexps');
    }
}
