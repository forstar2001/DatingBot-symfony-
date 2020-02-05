<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Illuminate\Support\Facades\Validator;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Dialog
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $scenario_id
 * @property int $language_id
 * 
 * @property \App\Models\Language $language
 * @property \App\Models\Scenario $scenario
 * @property \Illuminate\Database\Eloquent\Collection $dialog_participants
 * @property \Illuminate\Database\Eloquent\Collection $messages
 *
 * @package App\Models
 */
class Dialog extends Eloquent
{
	protected $casts = [
		'scenario_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'scenario_id',
		'language_id',
        'source_id',
        'current_step',
        'current_step_created_date'
	];

	protected $appends = [
	    'chat_plain'
    ];

	protected function getChatPlainAttribute() {
	    $text = "";
	    $bot = $this->dialog_participant->bot_profile;
	    $person = $this->dialog_participant->person_profile;
	    foreach($this->messages as $message) {
	        if ($message->is_sent || $message->sender_id == $person->id)
	            $text .= $message->created_at . ': ' . ($message->sender_id == $bot->id ? $bot->name : 'User') . ': ' . $message->message_text . PHP_EOL;
        }
        return $text;
    }

	public function language()
	{
		return $this->belongsTo(\App\Models\Language::class);
	}

    public function source()
    {
        return $this->belongsTo(\App\Models\Source::class);
    }

	public function scenario()
	{
		return $this->belongsTo(\App\Models\Scenario::class);
	}

	public function dialog_participants()
	{
		return $this->hasMany(\App\Models\DialogParticipant::class);
	}

    public function dialog_participant()
    {
        return $this->hasOne(\App\Models\DialogParticipant::class);
    }

	public function messages()
	{
		return $this->hasMany(\App\Models\Message::class);
	}

    public function getValidator()
    {
        $data = array(
            'scenario_id' => $this->scenario_id,
            'source_id' => $this->source_id
        );

        $rules = array(
            'scenario_id' => 'required|exists:scenarios,id',
            'source_id' => 'required|exists:sources,id'
        );

        $messages = array(
            'scenario_id.exists' => 'Given scenario is not exist in DB',
        );

        return Validator::make($data, $rules, $messages);
    }
}
