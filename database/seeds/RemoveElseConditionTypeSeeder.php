<?php

use Illuminate\Database\Seeder;
use App\Models\ConditionType;

class RemoveElseConditionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConditionType::where('name', 'else')->delete();
    }
}
