<?php


use Illuminate\Database\Seeder;

class AddSourcesBotProfilesRoleCategorySeeder extends Seeder
{
    public function run(){
        DB::table('role_categories')->insert([
            'name' => 'Sources BotProfiles',
        ]);
    }
}