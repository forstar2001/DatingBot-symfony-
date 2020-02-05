<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDialogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dialogs', function (Blueprint $table) {
            $table->integer('source_id')->unsigned();
            $table->foreign('source_id')
                ->references('id')
                ->on('sources')
                ->onDelete('cascade');
            $table->integer('current_step')->unsigned()->nullable();
            $table->dateTime('current_step_created_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dialogs', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
            $table->dropColumn('source_id');
            $table->dropColumn('current_step');
            $table->dropColumn('current_step_created_date');
        });
    }
}
