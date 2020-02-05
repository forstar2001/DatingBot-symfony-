<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueKeyLanguageTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_template_contents', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['message_template_id']);
            $table->dropUnique(['message_template_id', 'language_id']);
            $table->foreign('message_template_id')->references('id')->on('message_templates');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_template_contents', function (Blueprint $table) {
            $table->unique(['message_template_id', 'language_id']);
        });
    }
}
