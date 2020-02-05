<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

class Country extends Eloquent
{
    protected $fillable = [
        'name',
        'code',
        'additional_code',
        'language_id'
    ];

    public function regions() {
        return $this->hasMany(\App\Models\Region::class);
    }

    public function language() {
        return $this->belongsTo(\App\Models\Language::class);
    }

    public function getValidator() {
        $data = array(
            'name' => $this->name,
            'code' => $this->code,
            'additional_code' => $this->additional_code
        );

        $rules = array(
            'name' => 'required|max:255',
            'code' => 'nullable|max:50',
            'additional_code' => 'nullable|max:10'
        );

        $messages = array(
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
