<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
           [
               'name' => 'Super Admin',
               'description' => 'Super Admin'
           ],
           [
               'name' => 'Profiles Editor (Sweden)',
               'description' => 'Profiles Editor (Sweden)'
           ],
            [
                'name' => 'Profiles Editor (Finland)',
                'description' => 'Profiles Editor (Finland)'
            ],
        ]);

        DB::table('role_actions')->insert([
            [
                'name' => 'view all'
            ],
            [
                'name' => 'view own'
            ],
            [
                'name' => 'create'
            ],
            [
                'name' => 'remove all'
            ],
            [
                'name' => 'remove own'
            ],
            [
                'name' => 'edit all'
            ],
            [
                'name' => 'edit own'
            ]
        ]);

        DB::table('role_categories')->insert([
            [
                'name' => 'Scenarios',
            ],
            [
                'name' => 'Rules'
            ],
            [
                'name' => 'Conditions'
            ],
            [
                'name' => 'Languages'
            ],
            [
                'name' => 'Dictionaries'
            ],
            [
                'name' => 'Profile detail types'
            ],
            [
                'name' => 'Bot profiles'
            ],
            [
                'name' => 'Translations'
            ],
            [
                'name' => 'Sources'
            ],
            [
                'name' => 'Countries'
            ],
            [
                'name' => 'Regions'
            ],
            [
                'name' => 'Cities'
            ]
        ]);

    }
}
