<?php

use Illuminate\Database\Seeder;

class RoleCategoryNewConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_categories')->insert([
            [
                'name' => 'All Time Conditions'
            ],
            [
                'name' => 'All Time Non Response Conditions'
            ],
            [
                'name' => 'Make No Sence Conditions'
            ]
        ]);
    }
}
