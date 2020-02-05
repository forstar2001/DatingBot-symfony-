<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class BotProfile
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * 
 * @property \App\Models\Source $source
 * @property \Illuminate\Database\Eloquent\Collection $bot_profile_details
 * @property \Illuminate\Database\Eloquent\Collection $dialog_participants
 *
 * @package App\Models
 */
class BotProfile extends Eloquent
{
    const statusApproved = 'Approved';
    const statusPending = 'Pending';
    const statusRejected = 'Rejected';


	protected $casts = [
        'region_id' => 'integer|null'
	];

	protected $fillable = [
		'name',
        'status',
        'user_id',
        'domain',
        'region_id'
	];


    public function sources()
    {
        return $this->belongsToMany(Source::class, 'sources_bot_profiles');
	}

    public function sourcesBotProfiles()
    {
        return $this->hasMany(SourcesBotProfiles::class);
    }

	public function country() {
	    return $this->belongsTo(\App\Models\Country::class);
    }

    public function region() {
	    return $this->belongsTo(\App\Models\Region::class);
    }

	public function bot_profile_details()
	{
		return $this->hasMany(\App\Models\BotProfileDetail::class);
	}

	public function dialog_participants()
	{
		return $this->hasMany(\App\Models\DialogParticipant::class);
	}

	public function user() {
	    return $this->belongsTo(\App\Models\User::class);
    }

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'country_id' => $this->country_id,
            'domain' => $this->domain,
            'region_id' => $this->region_id
        );

        $rules = array(
            'name' => 'required|max:100',
            'status' => 'required|max:20',
            'user_id' => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'domain' => 'required|max:255',
            'region_id' => 'sometimes|exists:regions,id|nullable'
        );

        $messages = array(
            'name.max' => 'Max length of the field "name" is 100 charachters',
        );

        return Validator::make($data, $rules, $messages);
    }
}
