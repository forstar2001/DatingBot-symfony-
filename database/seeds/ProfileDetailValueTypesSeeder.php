<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileDetailValueTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('profile_detail_value_types')->insert([
            [
                'name' => 'decimal',
            ],
            [
                'name' => 'image'
            ],
            [
                'name' => 'document'
            ],
            [
                'name' => 'time'
            ],
            [
                'name' => 'Single dictionary value'
            ],
            [
                'name' => 'Multiple dictionary value'
            ],
            [
                'name' => 'Country'
            ],
            [
                'name' => 'City'
            ],
            [
                'name' => 'Region'
            ]
        ]);
    }
}
