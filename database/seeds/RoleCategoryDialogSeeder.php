<?php

use Illuminate\Database\Seeder;

class RoleCategoryDialogSeeder extends Seeder
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
                'name' => 'Conversations'
            ]
        ]);
    }
}
