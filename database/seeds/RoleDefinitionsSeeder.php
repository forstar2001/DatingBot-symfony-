<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RoleAction;
use App\Models\RoleCategorie;

class RoleDefinitionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_definitions')->insert([
            [
                'role_id' => Role::where('name', 'Profiles Editor (Sweden)')->first()->id,
                'action_id' => RoleAction::where('name', 'view all')->first()->id,
                'category_id' => RoleCategorie::where('name', 'Bot Profiles')->first()->id,
                'custom_condition' => 'country.code,SE'
            ],
            [
                'role_id' => Role::where('name', 'Profiles Editor (Sweden)')->first()->id,
                'action_id' => RoleAction::where('name', 'edit all')->first()->id,
                'category_id' => RoleCategorie::where('name', 'Bot Profiles')->first()->id,
                'custom_condition' => 'country.code,SE'
            ],
            [
                'role_id' => Role::where('name', 'Profiles Editor (Sweden)')->first()->id,
                'action_id' => RoleAction::where('name', 'remove own')->first()->id,
                'category_id' => RoleCategorie::where('name', 'Bot Profiles')->first()->id,
                'custom_condition' => 'country.code,SE'
            ],
        ]);
    }
}
