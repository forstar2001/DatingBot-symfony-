<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_categories', function(Blueprint $table) {
           $table->increments('id');
           $table->timestamps();
           $table->string('name');
           $table->unique('name');
        });

        Schema::create('role_actions', function(Blueprint $table) {
           $table->increments('id');
           $table->timestamps();
           $table->string('name');
           $table->unique('name');
        });

        Schema::create('roles', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('custom_condition')->nullable();
            $table->integer('category_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('role_categories');
            $table->foreign('role_id')->references('id')->on('role_actions');
            $table->unique(['role_id', 'category_id', 'custom_condition']);
        });

        Schema::create('user_roles', function(Blueprint $table) {
           $table->increments('id');
           $table->timestamps();
           $table->integer('user_id')->unsigned();
           $table->integer('role_id')->unsigned();
           $table->foreign('user_id')->references('id')->on('users');
           $table->foreign('role_id')->references('id')->on('roles');
           $table->unique(['user_id', 'role_id']);
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
