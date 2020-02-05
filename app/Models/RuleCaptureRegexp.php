<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**

 *
 * @package App\Models
 */
class RuleCaptureRegexp extends Eloquent
{
    protected $casts = [
        'profile_detail_type_id' => 'int',
        'rule_id' => 'int'
    ];

    public $timestamps = false;

    protected $fillable = [
        'regexp',
        'profile_detail_type_id',
        'rule_id',
    ];


    public function rule()
    {
        return $this->belongsTo(\App\Models\Rule::class);
    }

    public function profile_detail_type()
    {
        return $this->belongsTo(\App\Models\ProfileDetailType::class);
    }


    public function getValidator() {
        $data = array(
            'regexp' => $this->regexp,
            'rule_id' => $this->rule_id,
            'profile_detail_type_id' => $this->profile_detail_type_id
        );

        $rules = array(
            'regexp' => 'required|max:1000',
            'rule_id' => 'required|exists:rules,id',
            'profile_detail_type_id' => 'required|exists:profile_detail_types,id'
        );

        $messages = array(
        );

        return Validator::make($data, $rules, $messages);
    }

}
