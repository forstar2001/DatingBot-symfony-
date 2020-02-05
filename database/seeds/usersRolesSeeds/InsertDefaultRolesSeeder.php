<?php


use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsertDefaultRolesSeeder extends Seeder
{
    public function run()
    {
        $this->createSuperAdmin();
        $this->createCountryAdmin();
        $this->createEditor();
        $this->createWriter();
        $this->createScenarist();
    }

    private function createSuperAdmin()
    {
        $role = Role::where('name', 'Super Admin')->first();
        if (!isset($role)) {
            DB::table('roles')->insert([
                'name' => 'Super Admin',
                'description' => 'Super Admin'
            ]);
        }
    }

    private function createCountryAdmin()
    {
        $role = Role::where('name', 'Sweden Admin')->first();
        if (!isset($role)) {
            DB::table('roles')->insert([
                'name' => 'Sweden Admin',
                'description' => 'Sweden Admin'
            ]);
        }
    }

    private function createEditor()
    {
        $role = Role::where('name', 'Editor')->first();
        if (!isset($role)) {
            DB::table('roles')->insert([
                'name' => 'Editor',
                'description' => 'Editor'
            ]);
        }
    }

    private function createWriter()
    {
        $role = Role::where('name', 'Writer')->first();
        if (!isset($role)) {
            DB::table('roles')->insert([
                'name' => 'Writer',
                'description' => 'Writer'
            ]);
        }
    }

    private function createScenarist()
    {
        $role = Role::where('name', 'Scenarist')->first();
        if (!isset($role)) {
            DB::table('roles')->insert([
                'name' => 'Scenarist',
                'description' => 'Scenarist'
            ]);
        }
    }
}