<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSourcesBotProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sources_bot_profiles', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->nullable();
            $table->string('link')->nullable();
            $table->foreign('status_id')->references('id')->on('source_bot_profile_statuses');
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
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
            $table->dropColumn('link');
        });
    }
}
