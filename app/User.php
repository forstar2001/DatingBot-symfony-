<?php

namespace App;

use App\Models\TwoFactorAuthSecret;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','two_factor_auth_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        );

        $rules = array(
            'name' => 'required|max:100',
            'email' => 'required|email',
            'password' => 'required|min:6',
        );

        $messages = array(
            'name.max' => 'Max length of the field "name" is 100 characters',
            'password.min' => 'Minimal length of the field "password" is 6 characters'
        );

        return Validator::make($data, $rules, $messages);;
    }

    public function roles() {
        return $this->hasMany(\App\Models\UserRole::class);
    }

    public function botProfile() {
        return $this->hasMany(\App\Models\BotProfile::class);
    }

    public function isAdmin() {
        return $this->roles()->whereHas('role', function($q){
            $q->where('name', 'Super Admin');
        })->count() > 0;
    }

    public function hasRole($role) {
        return $this->roles()->whereHas('role', function($q) use ($role){
                $q->where('name', $role);
            })->count() > 0;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function twoFactorAuthSecret(){
        return $this->hasOne(TwoFactorAuthSecret::class);
    }
}
