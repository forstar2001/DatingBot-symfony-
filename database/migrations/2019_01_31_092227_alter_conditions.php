<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conditions_alltime', function(Blueprint $table) {
           $table->text('condition')->change();
           $table->text('result_message')->change();
        });
        Schema::table('conditions_nosence', function(Blueprint $table) {
            $table->text('condition')->change();
            $table->text('result_message')->change();
        });
        Schema::table('nonresponse_conditions_alltime', function(Blueprint $table) {
            $table->text('result_message')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
