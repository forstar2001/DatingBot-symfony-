<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

class Region extends Eloquent
{
    protected $fillable = [
        'name',
        'code',
        'additional_code',
        'country_id'
    ];

    protected $casts = [
        'code' => 'string|null',
        'additional_code' => 'string|null'
    ];

    public function cities() {
        return $this->hasMany(\App\Models\City::class);
    }

    public function country() {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function getValidator() {
        $data = array(
            'name' => $this->name,
            'code' => $this->code,
            'additional_code' => $this->additional_code,
            'country_id' => $this->country_id
        );

        $rules = array(
            'name' => 'required|max:255',
            'code' => 'nullable|max:50',
            'additional_code' => 'nullable|max:10',
            'country_id' => 'required|exists:countries,id'
        );

        $messages = array(
            'country_id.exists' => 'Country does not exist in DB'
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
