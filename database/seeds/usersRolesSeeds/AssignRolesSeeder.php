<?php


use App\Helpers\RoleAssignHelper;
use App\Models\Country;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AssignRolesSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $this->assignAsSuperAdmin();
            $this->assignAsCountryAdmin();
            $this->assignAsEditor();
            $this->assignAsWriter();
            $this->assignAsScenarist();
        });
    }


    private function assignAsSuperAdmin()
    {
        $user = User::where('email', 'super@mail.com')->first();
        if(isset($user)){
            throw new \Exception('User Already Exist');
        }
        DB::table('users')->insert([
            'name' => 'Super',
            'email' => 'super@mail.com',
            'password' => Hash::make('123456')
        ]);
        $user = User::where('email', 'super@mail.com')->first();

        RoleAssignHelper::assignAsSuperAdmin($user);
    }

    private function assignAsCountryAdmin()
    {
        $user = User::where('email', 'swedenadmin@mail.com')->first();
        if(isset($user)){
            throw new \Exception('User Already Exist');
        }
        $countryId = Country::where('name', 'Sweden')->first()->id;
        if(!isset($countryId)){
            throw new \Exception('Country do not Exist');
        }
        DB::table('users')->insert([
            'name' => 'SwedenAdmin',
            'email' => 'swedenadmin@mail.com',
            'password' => Hash::make('123456')
        ]);
        $user = User::where('email', 'swedenadmin@mail.com')->first();

        RoleAssignHelper::assignAsCountryAdmin($user, $countryId);
    }

    private function assignAsEditor()
    {
        $user = User::where('email', 'editor@mail.com')->first();
        if(isset($user)){
            throw new \Exception('User Already Exist');
        }
        $countryId = Country::where('name', 'Sweden')->first()->id;
        if(!isset($countryId)){
            throw new \Exception('Country do not Exist');
        }
        DB::table('users')->insert([
            'name' => 'Editor',
            'email' => 'editor@mail.com',
            'password' => Hash::make('123456')
        ]);
        $user = User::where('email', 'editor@mail.com')->first();

        RoleAssignHelper::assignAsEditor($user, $countryId);
    }

    private function assignAsWriter()
    {
        $user = User::where('email', 'writer@mail.com')->first();
        if(isset($user)){
            throw new \Exception('User Already Exist');
        }
        $countryId = Country::where('name', 'Sweden')->first()->id;
        if(!isset($countryId)){
            throw new \Exception('Country do not Exist');
        }
        DB::table('users')->insert([
            'name' => 'Writer',
            'email' => 'writer@mail.com',
            'password' => Hash::make('123456')
        ]);
        $user = User::where('email', 'writer@mail.com')->first();

        RoleAssignHelper::assignAsWriter($user, $countryId);
    }

    private function assignAsScenarist()
    {
        $user = User::where('email', 'scenarist@mail.com')->first();
        if(isset($user)){
            throw new \Exception('User Already Exist');
        }
        $countryId = Country::where('name', 'Sweden')->first()->id;
        if(!isset($countryId)){
            throw new \Exception('Country do not Exist');
        }
        DB::table('users')->insert([
            'name' => 'Scenarist',
            'email' => 'scenarist@mail.com',
            'password' => Hash::make('123456')
        ]);
        $user = User::where('email', 'scenarist@mail.com')->first();

        RoleAssignHelper::assignAsScenarist($user, $countryId);
    }
}