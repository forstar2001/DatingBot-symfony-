<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class NonresponseCondition
 *
 *
 * @package App\Models
 */
class NonresponseConditionAlltime extends Eloquent
{
    protected $table = 'nonresponse_conditions_alltime';
    public $timestamps = false;

    protected $casts = [
        'order' => 'int',
        'nonresponse_time' => 'int',
        'timing_min' => 'int',
        'timing_max' => 'int'
    ];

    protected $fillable = [
        'order',
        'nonresponse_time',
        'timing_min',
        'timing_max',
        'result_message'
    ];


    public function getValidator() {
        $data = array(
            'order' => $this->order,
            'nonresponse_time' => $this->nonresponse_time,
            'timing_min' => $this->timing_min,
            'timing_max' => $this->timing_max,
            'result_message' => $this->result_message
        );

        $rules = array(
            'order' => 'required|integer',
            'nonresponse_time' => 'required|integer',
            'timing_min' => 'required|integer',
            'timing_max' => 'required|integer',
            'result_message' => 'required'
        );

        $messages = array(
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
