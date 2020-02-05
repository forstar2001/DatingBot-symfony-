<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class Message
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $dialog_id
 * @property string $message_text
 * @property int $sender_id
 * @property int $receiver_id
 * 
 * @property \App\Models\Dialog $dialog
 * @property \App\Models\DialogParticipant $dialog_participant
 *
 * @package App\Models
 */
class Message extends Eloquent
{
	protected $casts = [
		'dialog_id' => 'int',
		'sender_id' => 'int',
		'receiver_id' => 'int'
	];

	protected $fillable = [
		'dialog_id',
		'message_text',
		'sender_id',
		'receiver_id',
        'datetime_to_send',
        'delay',
        'typing_delay'
	];

	public function dialog()
	{
		return $this->belongsTo(\App\Models\Dialog::class);
	}

	public function dialog_participant()
	{
		return $this->belongsTo(\App\Models\DialogParticipant::class, 'sender_id');
	}
    public function getValidator()
    {
        $data = array(
            'dialog_id' => $this->dialog_id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message_text' => $this->message_text
        );

        $rules = array(
            'dialog_id' => 'required|exists:dialogs,id',
            'sender_id' => 'required|exists:person_profiles,id',
            'receiver_id' => 'required|exists:bot_profiles,id',
            'message_text' => 'required|max:10000'
        );

        $messages = array(
            'dialog_id.exists' => 'Given dialog id doesn\'t exists in db',
            'sender_id.exists' => 'Given sender id doesn\'t exists in DB',
            'receiver_id.exists' => 'Given receiver id doesn\'t exists in DB',
            'message_text.max' => 'Maximum length of message is 10000 characters'
        );

        return Validator::make($data, $rules, $messages);
    }

}
