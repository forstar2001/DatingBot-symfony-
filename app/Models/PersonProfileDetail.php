<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class PersonProfileDetail
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $person_profile_id
 * @property int $profile_detail_type_id
 * @property string $value
 * 
 * @property \App\Models\PersonProfile $person_profile
 * @property \App\Models\ProfileDetailType $profile_detail_type
 *
 * @package App\Models
 */
class PersonProfileDetail extends Eloquent
{
	protected $casts = [
		'person_profile_id' => 'int',
		'profile_detail_type_id' => 'int'
	];

	protected $fillable = [
		'person_profile_id',
		'profile_detail_type_id',
		'value'
	];

	public function person_profile()
	{
		return $this->belongsTo(\App\Models\PersonProfile::class);
	}

	public function profile_detail_type()
	{
		return $this->belongsTo(\App\Models\ProfileDetailType::class);
	}

    public function getValidator()
    {
        $data = array(
            'person_profile_id' => $this->person_profile_id,
            'profile_detail_type_id' => $this->profile_detail_type_id,
            'value' => $this->value
        );

        $rules = array(
            'person_profile_id' => 'required|exists:person_profiles,id',
            'profile_detail_type_id' => 'required|exists:profile_detail_types,id',
            'value' => 'required|max:1000'
        );

        $messages = array(
            'person_profile_id.exists' => 'Given person profile id doesn\'t exists in db',
            'profile_detail_type_id' => 'Given profile detail type id doesn\'t exists in DB',
            'value.max' => 'Maximum length of detail value is 1000 characters'
        );

        return Validator::make($data, $rules, $messages);
    }
}
