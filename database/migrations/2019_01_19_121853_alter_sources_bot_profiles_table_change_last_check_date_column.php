<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSourcesBotProfilesTableChangeLastCheckDateColumn extends Migration
{

    public function up()
    {
        Schema::table('sources_bot_profiles', function (Blueprint $table) {
            $table->dateTime('last_check_date')->nullable()->change();
        });
    }


    public function down()
    {

    }
}
