<?php

use Illuminate\Database\Seeder;

class RolesAdditionalSeeder extends Seeder
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
                'name' => 'Translations Content'
            ],
            [
                'name' => 'Non-response Conditions'
            ],
            [
                'name' => 'Bot Profile Details'
            ],
            [
                'name' => 'Profile Detail Value Types'
            ],
            [
                'name' => 'Roles'
            ],
            [
                'name' => 'Users'
            ],
            [
                'name' => 'Assigning Roles to User'
            ],
            [
                'name' => 'Test Scenarios'
            ]
        ]);
    }
}
