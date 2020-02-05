<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_status_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 255);
            $table->unique('name');
        });

        Schema::table('role_definitions', function (Blueprint $table) {
           $table->integer('country_id')->unsigned()->nullable();
           $table->foreign('country_id')->references('id')
               ->on('countries')
              ->onDelete('cascade');
           $table->integer('profile_status_type_id')->unsigned()->nullable();
           $table->foreign('profile_status_type_id')->references('id')
               ->on('profile_status_types')
               ->onDelete('cascade');
           $table->dropForeign('role_definitions_category_id_foreign');
           $table->dropForeign('role_definitions_action_id_foreign');
           $table->dropForeign('role_definitions_role_id_foreign');
           $table->dropIndex('role_definitions_action_id_category_id_role_id_unique');
           $table->unique(['action_id', 'category_id', 'role_id', 'country_id', 'profile_status_type_id'], 'role_definitions_uk');
           $table->foreign('role_id')->references('id')
                ->on('roles')
                ->onDelete('cascade');
           $table->foreign('action_id')->references('id')
                ->on('role_actions')
                ->onDelete('cascade');
           $table->foreign('category_id')->references('id')
                ->on('role_categories')
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
