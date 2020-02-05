<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class Scenario
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property int $user_id
 * @property int $type_id
 * 
 * @property \App\Models\ScenarioType $scenario_type
 * @property \App\Models\User $user
 * @property \Illuminate\Database\Eloquent\Collection $dialogs
 * @property \Illuminate\Database\Eloquent\Collection $rules
 *
 * @package App\Models
 */
class Scenario extends Eloquent
{
	protected $casts = [
		'user_id' => 'int',
		'type_id' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'user_id',
		'type_id'
	];

	public function scenario_type()
	{
		return $this->belongsTo(\App\Models\ScenarioType::class, 'type_id');
	}

	public function user()
	{
		return $this->belongsTo(\App\Models\User::class);
	}

	public function dialogs()
	{
		return $this->hasMany(\App\Models\Dialog::class);
	}

	public function rules()
	{
		return $this->hasMany(\App\Models\Rule::class);
	}

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'type_id' => $this->type_id
        );

        $rules = array(
            'name' => 'required|max:100',
            'description' => 'required|max:1000',
            'user_id' => 'required|exists:users,id',
            'type_id' => 'required|exists:scenario_types,id'
        );

        $messages = array(
            'user_id.exists' => 'Given user does not exist in DB',
            'type_id.exists' => 'Given scenario_type_id does not exist in DB'
        );

        return Validator::make($data, $rules, $messages);
    }

}
