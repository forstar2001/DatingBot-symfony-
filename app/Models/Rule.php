<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class Rule
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property int $order
 * @property int $scenario_id
 * 
 * @property \App\Models\Scenario $scenario
 * @property \Illuminate\Database\Eloquent\Collection $conditions
 * @property \Illuminate\Database\Eloquent\Collection $nonresponse_conditions
 *
 * @package App\Models
 */
class Rule extends Eloquent
{
	protected $casts = [
		'order' => 'int',
		'scenario_id' => 'int'
	];

	protected $fillable = [
		'name',
		'order',
		'scenario_id',
	];


	protected $appends = [
	    'conditions_expression'
    ];

	public function scenario()
	{
		return $this->belongsTo(\App\Models\Scenario::class);
	}

	public function conditions()
	{
		return $this->hasMany(\App\Models\Condition::class);
	}

	public function nonresponse_conditions()
	{
		return $this->hasMany(\App\Models\NonresponseCondition::class);
	}

	public function capture_regexps()
    {
        return $this->hasMany(\App\Models\RuleCaptureRegexp::class);
    }

	public function getValidator() {
        $data = array(
            'name' => $this->name,
            'order' => $this->order,
            'scenario_id' => $this->scenario_id
        );

        $rules = array(
            'name' => 'required|max:100',
            'order' => 'required|numeric|max:10000',
            'scenario_id' => 'required|exists:scenarios,id'
        );

        $messages = array(
            'scenario_id.exists' => 'Given scenario does not exist in DB'
        );

        return Validator::make($data, $rules, $messages);
    }

    protected function getConditionsExpressionAttribute() {
	    $conditions = $this->conditions->sortBy('order')->all();
	    $expression = '';
	    foreach ($conditions as $key => $value) {
	        if ($value->condition_type->name == 'if') {
                $expression .= "if (" . $value->condition . ") " . PHP_EOL .
                    "{" . PHP_EOL .
                    "    return '" . $value->result_message . "'" . PHP_EOL .
                    "}" . PHP_EOL;
            } else if ($value->condition_type->name == 'else if') {
	            $expression .= "else if (" . $value->condition . ") " . PHP_EOL .
                    "{" . PHP_EOL .
                    "    return '" . $value->result_message . "'" .  PHP_EOL .
                    "}" . PHP_EOL;
            } else if ($value->condition_type->name == 'else') {
                $expression .= "else"  . PHP_EOL .
                    "{" . PHP_EOL .
                    "    return '" . $value->result_message . "'" . PHP_EOL .
                    "}" . PHP_EOL;
            }
        }

        return $expression;
    }
}
