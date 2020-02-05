<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('template_categories');

        Schema::create('template_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('variable_name');
            $table->unique('name');
            $table->unique('variable_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_categories');
    }
}
