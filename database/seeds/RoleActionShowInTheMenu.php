<?php

use Illuminate\Database\Seeder;

class RoleActionShowInTheMenu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_actions')->insert([
            [
                'name' => 'show in the menu'
            ]
        ]);
    }
}
