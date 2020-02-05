<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class Source
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $bot_profiles
 * @property \Illuminate\Database\Eloquent\Collection $person_profiles
 *
 * @package App\Models
 */
class Source extends Eloquent
{
	protected $fillable = [
		'name'
	];

	public function botProfiles()
	{
		return $this->belongsToMany(\App\Models\BotProfile::class, 'sources_bot_profiles');
	}

    public function sourcesBotProfiles()
    {
        return $this->hasMany(SourcesBotProfiles::class);
    }

	public function person_profiles()
	{
		return $this->hasMany(\App\Models\PersonProfile::class);
	}

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
        );

        $rules = array(
            'name' => 'required|max:100',
        );

        $messages = array(
        );

        return Validator::make($data, $rules, $messages);
    }
}
