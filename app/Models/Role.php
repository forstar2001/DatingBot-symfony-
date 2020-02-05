<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

class Role extends Eloquent
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'action_id',
        'role_id',
        'country_id',
        'profile_status_type_id'
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string'
    ];

    public function role_definitions() {
        return $this->hasMany(\App\Models\RoleDefinition::class);
    }

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
            'description' => $this->description,
        );

        $rules = array(
            'name' => 'required|max:255',
            'description' => 'required|max:255'
        );

        $messages = array(
            'name.max' => 'Max length of the field "name" is 255 characters',
            'description.max' => 'Max length of the field "description" is 255 characters'
        );

        return Validator::make($data, $rules, $messages);
    }

}
