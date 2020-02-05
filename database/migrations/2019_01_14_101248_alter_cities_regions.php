<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCitiesRegions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('code', 50)->change();
            $table->string('additional_code', 50)->change();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->string('code', 50)->change();
            $table->string('additional_code', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('code', 10)->change();
            $table->string('additional_code', 10)->change();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->string('code', 10)->change();
            $table->string('additional_code', 10)->change();
        });
    }
}
