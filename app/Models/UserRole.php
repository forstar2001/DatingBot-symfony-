<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

class UserRole extends Eloquent
{
    protected $fillable = [
        'user_id',
        'role_id'
    ];

    protected $casts = [
        'user_id' => 'int',
        'role_id' => 'int'
    ];


    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function role() {
        return $this->belongsTo(\App\Models\Role::class);
    }

    public function getValidator()
    {
        $data = array(
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
        );

        $rules = array(
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        );

        $messages = array(
        );

        return Validator::make($data, $rules, $messages);
    }

}
