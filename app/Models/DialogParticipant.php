<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class DialogParticipant
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $dialog_id
 * @property int $bot_profile_id
 * @property int $person_profile_id
 * 
 * @property \App\Models\BotProfile $bot_profile
 * @property \App\Models\Dialog $dialog
 * @property \App\Models\PersonProfile $person_profile
 * @property \Illuminate\Database\Eloquent\Collection $messages
 *
 * @package App\Models
 */
class DialogParticipant extends Eloquent
{
	protected $casts = [
		'dialog_id' => 'int',
		'bot_profile_id' => 'int',
		'person_profile_id' => 'int'
	];

	protected $fillable = [
		'dialog_id',
		'bot_profile_id',
		'person_profile_id'
	];

	public function bot_profile()
	{
		return $this->belongsTo(\App\Models\BotProfile::class);
	}

	public function dialog()
	{
		return $this->belongsTo(\App\Models\Dialog::class);
	}

	public function person_profile()
	{
		return $this->belongsTo(\App\Models\PersonProfile::class);
	}

	public function messages()
	{
		return $this->hasMany(\App\Models\Message::class, 'sender_id');
	}

    public function getValidator()
    {
        $data = array(
            'dialog_id' => $this->dialog_id,
            'bot_profile_id' => $this->bot_profile_id,
            'person_profile_id' => $this->person_profile_id
        );

        $rules = array(
            'dialog_id' => 'required|exists:dialogs,id',
            'bot_profile_id' => 'required',
            'person_profile_id' => 'required'
        );

        $messages = array(
        );

        return Validator::make($data, $rules, $messages);
    }
}
