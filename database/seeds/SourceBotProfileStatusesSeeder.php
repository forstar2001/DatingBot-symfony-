<?php

use Illuminate\Database\Seeder;

class SourceBotProfileStatusesSeeder extends Seeder
{
    public function run()
    {
        DB::table('source_bot_profile_statuses')->insert([
            'status' => 'Active',
        ]);
        DB::table('source_bot_profile_statuses')->insert([
            'status' => 'Paused',
        ]);
        DB::table('source_bot_profile_statuses')->insert([
            'status' => 'Pending',
        ]);
        DB::table('source_bot_profile_statuses')->insert([
            'status' => 'Broken',
        ]);
    }
}