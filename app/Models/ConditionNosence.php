<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**

 */
class ConditionNosence extends Eloquent
{
    protected $table = 'conditions_nosence';
    public $timestamps = false;

    protected $casts = [
        'order' => 'int',
        'timing_min' => 'int',
        'timing_max' => 'int'
    ];

    protected $fillable = [
        'order',
        'condition',
        'result_message',
        'timing_min',
        'timing_max'
    ];


    public function getValidator() {
        $data = array(
            'order' => $this->order,
            'condition' => $this->condition,
            'result_message' => $this->result_message,
            'timing_min' => $this->timing_min,
            'timing_max' => $this->timing_max
        );

        $rules = array(
            'order' => 'required|numeric|max:10000',
            'condition' => 'max:10000',
            'result_message' => 'required|max:10000',
            'timing_min' => 'required|numeric|max:1000',
            'timing_max' => 'required|numeric|max:1000'
        );

        $messages = array(
        );

        $validator = Validator::make($data, $rules, $messages);

        $validator->after(function ($validator) {
            if (!$this->checkConditionText()) {
                $validator->errors()->add('condition', 'Condition is not valid');
            }
            if (!$this->checkResultMessage()) {
                $validator->errors()->add('result_message', 'Result message is not valid');
            }
        });

        return $validator;
    }

    public function checkConditionText() {

        //TODO: checking condition text - check all bot profile details and person profile details

        return true;
    }

    public function checkResultMessage() {

        //TODO: check if any of used templates exist

        return true;
    }
}
