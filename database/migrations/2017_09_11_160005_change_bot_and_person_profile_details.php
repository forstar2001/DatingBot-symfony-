<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBotAndPersonProfileDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('bot_profile_details', function (Blueprint $table) {
            //$table->dropIndex('bot_profile_details_profile_detail_type_id_foreign');
            //$table->dropForeign('bot_profile_details_bot_profile_id_foreign');
            //$table->dropUnique('profile_detail_type_id');
            //$table->dropUnique(['profile_detail_type_id']);
            $table->unique(['bot_profile_id', 'profile_detail_type_id']);
            $table->foreign('profile_detail_type_id')->references('id')->on('profile_detail_types');
            $table->foreign('bot_profile_id')->references('id')->on('bot_profiles');
        });

        Schema::table('person_profile_details', function (Blueprint $table) {
            //$table->dropIndex('person_profile_details_profile_detail_type_id_foreign');
            //$table->dropForeign('person_profile_details_bot_profile_id_foreign');
            //$table->dropUnique('profile_detail_type_id');
            //$table->dropUnique(['profile_detail_type_id']);
            $table->unique(['person_profile_id', 'profile_detail_type_id'], 'pers_prof_details_pers_prof_id_prof_detail_type_id_uq');
            $table->foreign('profile_detail_type_id')->references('id')->on('profile_detail_types');
            $table->foreign('person_profile_id')->references('id')->on('person_profiles');

        });*/
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
