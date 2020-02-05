<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ConditionType
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $conditions
 *
 * @package App\Models
 */
class ConditionType extends Eloquent
{
	protected $fillable = [
		'name'
	];

	public function conditions()
	{
		return $this->hasMany(\App\Models\Condition::class);
	}
}
