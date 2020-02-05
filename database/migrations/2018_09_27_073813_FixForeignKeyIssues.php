<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixForeignKeyIssues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bot_profiles', function ($table) {
            $table->dropForeign('bot_profiles_source_id_foreign');

            $table->foreign('source_id')
                ->references('id')->on('sources')
                ->onDelete('cascade');
        });

        Schema::table('bot_profile_details', function($table) {
            $table->dropForeign('bot_profile_details_bot_profile_id_foreign');
            $table->dropForeign('bot_profile_details_profile_detail_type_id_foreign');

            $table->foreign('bot_profile_id')
                ->references('id')->on('bot_profiles')
                ->onDelete('cascade');
            $table->foreign('profile_detail_type_id')
                ->references('id')->on('profile_detail_types')
                ->onDelete('cascade');
        });
        Schema::table('cities', function($table) {
            $table->dropForeign('cities_country_id_foreign');

            $table->foreign('country_id')
                ->references('id')->on('countries')
                ->onDelete('cascade');
        });

        Schema::table('conditions', function($table) {
            $table->dropForeign('conditions_condition_type_id_foreign');
            $table->dropForeign('conditions_rule_id_foreign');

            $table->foreign('condition_type_id')
                ->references('id')->on('condition_types')
                ->onDelete('cascade');
            $table->foreign('rule_id')
                ->references('id')->on('rules')
                ->onDelete('cascade');
        });

        Schema::table('dialogs', function($table) {
            $table->dropForeign('dialogs_language_id_foreign');
            $table->dropForeign('dialogs_scenario_id_foreign');

            $table->foreign('language_id')
                ->references('id')->on('languages')
                ->onDelete('cascade');
            $table->foreign('scenario_id')
                ->references('id')->on('scenarios')
                ->onDelete('cascade');
        });

        Schema::table('dialog_participants', function($table) {
            $table->dropForeign('dialog_participants_bot_profile_id_foreign');
            $table->dropForeign('dialog_participants_dialog_id_foreign');
            $table->dropForeign('dialog_participants_person_profile_id_foreign');

            $table->foreign('bot_profile_id')
                ->references('id')->on('bot_profiles')
                ->onDelete('cascade');
            $table->foreign('dialog_id')
                ->references('id')->on('dialogs')
                ->onDelete('cascade');
            $table->foreign('person_profile_id')
                ->references('id')->on('person_profiles')
                ->onDelete('cascade');
        });

        Schema::table('messages', function($table) {
            $table->dropForeign('messages_dialog_id_foreign');

            $table->foreign('dialog_id')
                ->references('id')->on('dialogs')
                ->onDelete('cascade');
        });

        Schema::table('message_template_contents', function($table) {
            $table->dropForeign('message_template_contents_language_id_foreign');
            $table->dropForeign('message_template_contents_message_template_id_foreign');

            $table->foreign('language_id')
                ->references('id')->on('languages')
                ->onDelete('cascade');
            $table->foreign('message_template_id')
                ->references('id')->on('message_templates')
                ->onDelete('cascade');
        });

        Schema::table('nonresponse_conditions', function($table) {
            $table->dropForeign('nonresponse_conditions_rule_id_foreign');

            $table->foreign('rule_id')
                ->references('id')->on('rules')
                ->onDelete('cascade');
        });

        Schema::table('person_profiles', function($table) {
            $table->dropForeign('person_profiles_source_id_foreign');

            $table->foreign('source_id')
                ->references('id')->on('sources')
                ->onDelete('cascade');
        });

        Schema::table('person_profile_details', function($table) {
            $table->dropForeign('person_profile_details_person_profile_id_foreign');
            $table->dropForeign('person_profile_details_profile_detail_type_id_foreign');

            $table->foreign('person_profile_id')
                ->references('id')->on('person_profiles')
                ->onDelete('cascade');
            $table->foreign('profile_detail_type_id')
                ->references('id')->on('profile_detail_types')
                ->onDelete('cascade');
        });

        Schema::table('profile_detail_types', function($table) {
            $table->dropForeign('profile_detail_types_profile_detail_value_type_id_foreign');

            $table->foreign('profile_detail_value_type_id')
                ->references('id')->on('profile_detail_value_types')
                ->onDelete('cascade');
        });

        Schema::table('rules', function($table) {
            $table->dropForeign('rules_scenario_id_foreign');

            $table->foreign('scenario_id')
                ->references('id')->on('scenarios')
                ->onDelete('cascade');
        });

        Schema::table('scenarios', function($table) {
            $table->dropForeign('scenarios_type_id_foreign');
            $table->dropForeign('scenarios_user_id_foreign');

            $table->foreign('type_id')
                ->references('id')->on('scenario_types')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
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
