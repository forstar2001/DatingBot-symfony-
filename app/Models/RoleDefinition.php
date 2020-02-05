<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

class RoleDefinition extends Eloquent
{
    protected $fillable = [
        'custom_condition',
        'category_id',
        'action_id',
        'role_id',
        'country_id',
        'profile_status_type_id'
    ];

    protected $casts = [
        'custom_condition' => 'string',
        'category_id' => 'int',
        'action_id' => 'int',
        'role_id' => 'int'
    ];

    public function category() {
        return $this->belongsTo(\App\Models\RoleCategorie::class);
    }

    public function action() {
        return $this->belongsTo(\App\Models\RoleAction::class);
    }

    public function role() {
        return $this->belongsTo(\App\Models\Role::class);
    }

    public function country() {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function profile_status_type() {
        return $this->belongsTo(\App\Models\ProfileStatusType::class);
    }

    public function getValidator()
    {
        $data = array(
            'custom_condition' => $this->custom_condition,
            'category_id' => $this->category_id,
            'role_id' => $this->role_id,
            'action_id' => $this->action_id,
            'country_id' => $this->country_id,
            'profile_status_type_id' => $this->profile_status_type_id
        );

        $rules = array(
            'category_id' => 'required|exists:role_categories,id',
            'role_id' => 'required|exists:roles,id',
            'action_id' => 'required|exists:role_actions,id'
        );

        $messages = array(
        );

        return Validator::make($data, $rules, $messages);
    }

}
