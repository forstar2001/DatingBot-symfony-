<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function(Blueprint $table) {
           $table->dropForeign('roles_category_id_foreign');
           $table->dropForeign('roles_role_id_foreign');
           $table->dropColumn(['role_id', 'category_id', 'custom_condition']);
           $table->string('name', 255);
           $table->string('description', 255);
           $table->unique(['name']);
        });

        Schema::create('role_definitions', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('custom_condition')->nullable();
            $table->integer('category_id')->unsigned();
            $table->integer('action_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('role_categories')->onDelete('cascade');
            $table->foreign('action_id')->references('id')->on('role_actions')->onDelete('cascade');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unique(['action_id', 'category_id', 'role_id' ]);
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
