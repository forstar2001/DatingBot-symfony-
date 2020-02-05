<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ProfileDetailValueType
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $profile_detail_types
 *
 * @package App\Models
 */
class ProfileDetailValueType extends Eloquent
{
	protected $fillable = [
		'name'
	];

	public function profile_detail_types()
	{
		return $this->hasMany(\App\Models\ProfileDetailType::class);
	}
}
