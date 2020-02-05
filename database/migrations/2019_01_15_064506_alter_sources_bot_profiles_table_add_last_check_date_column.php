<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSourcesBotProfilesTableAddLastCheckDateColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sources_bot_profiles', function (Blueprint $table) {
            $table->string('last_check_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sources_bot_profiles', function (Blueprint $table) {
            $table->dropColumn('last_check_date');
        });
    }
}
