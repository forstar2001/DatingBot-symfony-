<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class Condition
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $order
 * @property int $condition_type_id
 * @property int $rule_id
 * @property string $condition
 * @property string $result_message
 * @property int $timing_min
 * @property int $timing_max
 * 
 * @property \App\Models\ConditionType $condition_type
 * @property \App\Models\Rule $rule
 *
 * @package App\Models
 */
class Condition extends Eloquent
{
	protected $casts = [
		'order' => 'int',
		'condition_type_id' => 'int',
		'rule_id' => 'int',
		'timing_min' => 'int',
		'timing_max' => 'int'
	];

	protected $fillable = [
		'order',
		'condition_type_id',
		'rule_id',
		'condition',
		'result_message',
		'timing_min',
		'timing_max'
	];

	public function condition_type()
	{
		return $this->belongsTo(\App\Models\ConditionType::class);
	}

	public function rule()
	{
		return $this->belongsTo(\App\Models\Rule::class);
	}

    public function getValidator() {
        $data = array(
            'condition_type_id' => $this->condition_type_id,
            'order' => $this->order,
            'rule_id' => $this->rule_id,
            'condition' => $this->condition,
            'result_message' => $this->result_message,
            'timing_min' => $this->timing_min,
            'timing_max' => $this->timing_max
        );

        $rules = array(
            'condition_type_id' => 'required|numeric|exists:condition_types,id',
            'order' => 'required|numeric|max:10000',
            'rule_id' => 'required|numeric|exists:rules,id',
            'condition' => 'max:10000',
            'result_message' => 'required|max:10000',
            'timing_min' => 'required|numeric|max:1000',
            'timing_max' => 'required|numeric|max:1000'
        );

        $messages = array(
            'rule_id.exists' => 'Given rule does not exist in DB',
            'condition_type_id.exists' => 'Given condition type does not exist in DB'
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
