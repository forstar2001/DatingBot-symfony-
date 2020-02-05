<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class PersonProfile
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property int $source_id
 * 
 * @property \App\Models\Source $source
 * @property \Illuminate\Database\Eloquent\Collection $dialog_participants
 * @property \Illuminate\Database\Eloquent\Collection $person_profile_details
 *
 * @package App\Models
 */
class PersonProfile extends Eloquent
{
	protected $casts = [
		'source_id' => 'int'
	];

	protected $fillable = [
		'name',
		'source_id'
	];

	public function source()
	{
		return $this->belongsTo(\App\Models\Source::class);
	}

	public function country() {
	    return $this->belongsTo(\App\Models\Country::class);
    }

    public function region() {
	    return $this->belongsTo(\App\Models\Region::class);
    }

	public function dialog_participants()
	{
		return $this->hasMany(\App\Models\DialogParticipant::class);
	}

	public function person_profile_details()
	{
		return $this->hasMany(\App\Models\PersonProfileDetail::class);
	}

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
            'source_id' => $this->source_id,
            'country_id' => $this->country_id,
            'region_id' => $this->region_id
        );

        $rules = array(
            'name' => 'required|max:100',
            'source_id' => 'required|exists:sources,id',
            'country_id' => 'required|exists:countries,id',
            'region' => 'exists:regions,id'
        );

        $messages = array(
            'name.max' => 'Max length of the field "name" is 100 charachters',
            'source.exists' => 'Given source not exists in DB'
        );

        return Validator::make($data, $rules, $messages);
    }
}
