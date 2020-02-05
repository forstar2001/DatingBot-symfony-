<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProfileDetailTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_detail_types', function (Blueprint $table) {
            $table->dropColumn(['reference_table_column', 'reference_id']);
            $table->integer('dictionary_id')->unsigned()->nullable();
            $table->foreign('dictionary_id')
                ->references('id')
                ->on('dictionaries')
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
