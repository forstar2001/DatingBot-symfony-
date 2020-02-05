<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

class City extends Eloquent
{
    protected $fillable = [
        'name',
        'code',
        'additional_code',
        'region_id'
    ];


    public function region() {
        return $this->belongsTo(\App\Models\Region::class);
    }

    public function getValidator() {
        $data = array(
            'name' => $this->name,
            'code' => $this->code,
            'additional_code' => $this->additional_code,
            'region_id' => $this->region_id
        );

        $rules = array(
            'name' => 'required|max:255',
            'code' => 'nullable|max:50',
            'additional_code' => 'nullable|max:10',
            'region_id' => 'required|exists:regions,id'
        );

        $messages = array(
            'region_id.exists' => 'Region does not exist in DB'
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
