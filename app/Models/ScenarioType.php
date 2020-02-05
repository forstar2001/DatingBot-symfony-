<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ScenarioType
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $scenarios
 *
 * @package App\Models
 */
class ScenarioType extends Eloquent
{
	protected $fillable = [
		'name'
	];

	public function scenarios()
	{
		return $this->hasMany(\App\Models\Scenario::class, 'type_id');
	}
}
