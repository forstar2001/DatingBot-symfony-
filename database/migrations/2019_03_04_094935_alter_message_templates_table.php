<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMessageTemplatesTable extends Migration
{
    public function up()
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropUnique(['variable_name']);
            $table->unique(['variable_name', 'template_category_id']);
        });
    }

    public function down()
    {
        //
    }
}
