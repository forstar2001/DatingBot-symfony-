<?php


use Illuminate\Database\Seeder;

class ClearSourcesBotProfilesSeeder extends Seeder
{
    public function run()
    {
        DB::table('sources_bot_profiles')->delete();
    }
}