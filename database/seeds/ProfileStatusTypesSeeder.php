<?php

use Illuminate\Database\Seeder;

class ProfileStatusTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('profile_status_types')->insert([
            [
                'name' => 'Pending'
            ],
            [
                'name' => 'Rejected'
            ],
            [
                'name' => 'Approved'
            ]
        ]);
    }
}
