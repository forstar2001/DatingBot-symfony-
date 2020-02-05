<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $scenarios
 *
 * @package App\Models
 */
class User extends Eloquent
{
	protected $hidden = [
		'password',
		'remember_token',
        'email'
	];

	protected $fillable = [
		'name',
		'password',
		'remember_token'
	];

	public function scenarios()
	{
		return $this->hasMany(\App\Models\Scenario::class);
	}

	public function roles() {
	    return $this->hasMany(\App\Models\UserRole::class);
    }

    public function isAdmin() {
        return $this->roles()->with('role')->has('role.name', 'Super Admin');
    }
}
