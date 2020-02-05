<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultDataToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('profile_detail_value_types')->insert([
                ['name' => 'integer'],
                ['name' => 'datetime']
            ]
        );

        DB::table('profile_detail_types')->insert([
            ['name' => 'First name', 'variable_name' => 'firstname', 'max_string_length' => 100, 'profile_detail_value_type_id' => 1],
            ['name' => 'Time zone', 'variable_name' => 'timezone', 'max_string_length' => 100, 'profile_detail_value_type_id' => 1]
        ]);

        DB::table('profile_detail_types')->insert([
            ['name' => 'City', 'variable_name' => 'city', 'reference_table_column' => 'cities.name', 'profile_detail_value_type_id' => 1],
            ['name' => 'Country', 'variable_name' => 'country', 'reference_table_column' => 'countries.name', 'profile_detail_value_type_id' => 1]
        ]);

        DB::table('languages')->insert([
            ['name' => 'Swedish', 'code' => 'SE'],
            ['name' => 'Danish', 'code' => 'DK'],
            ['name' => 'Dutch', 'code' => 'NL'],
            ['name' => 'German', 'code' => 'DE'],
            ['name' => 'Finnish', 'code' => 'FI'],
            ['name' => 'Czech', 'code' => 'CZ'],
            ['name' => 'Croatian', 'code' => 'CR'],
            ['name' => 'French', 'code' => 'FR'],
            ['name' => 'Swiss', 'code' => 'CH'],
            ['name' => 'English (Great Britain)', 'code' => 'ENGB'],
            ['name' => 'Spanish', 'code' => 'ES']
        ]);

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
