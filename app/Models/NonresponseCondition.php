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
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $order
 * @property int $nonresponse_time
 * @property int $rule_id
 * @property int $timing_min
 * @property int $timing_max
 * @property string $result_message
 * 
 * @property \App\Models\Rule $rule
 *
 * @package App\Models
 */
class NonresponseCondition extends Eloquent
{
	protected $casts = [
		'order' => 'int',
		'nonresponse_time' => 'int',
		'rule_id' => 'int',
		'timing_min' => 'int',
		'timing_max' => 'int'
	];

	protected $fillable = [
		'order',
		'nonresponse_time',
		'rule_id',
		'timing_min',
		'timing_max',
		'result_message'
	];

	public function rule()
	{
		return $this->belongsTo(\App\Models\Rule::class);
	}

    public function getValidator() {
        $data = array(
            'order' => $this->order,
            'nonresponse_time' => $this->nonresponse_time,
            'rule_id' => $this->rule_id,
            'timing_min' => $this->timing_min,
            'timing_max' => $this->timing_max,
            'result_message' => $this->result_message
        );

        $rules = array(
            'order' => 'required|integer',
            'nonresponse_time' => 'required|integer',
            'rule_id' => 'required|exists:rules,id',
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
