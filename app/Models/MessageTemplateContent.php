<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class MessageTemplateContent
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $message_template_id
 * @property int $language_id
 * @property string $text
 * 
 * @property \App\Models\Language $language
 * @property \App\Models\MessageTemplate $message_template
 *
 * @package App\Models
 */
class MessageTemplateContent extends Eloquent
{
	protected $casts = [
		'message_template_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'message_template_id',
		'language_id',
		'text'
	];

	public function language()
	{
		return $this->belongsTo(\App\Models\Language::class);
	}

	public function message_template()
	{
		return $this->belongsTo(\App\Models\MessageTemplate::class);
	}

    public function getValidator() {
        $data = array(
            'message_template_id' => $this->message_template_id,
            'language_id' => $this->language_id,
            'text' => $this->text
        );

        $rules = array(
            'message_template_id' => 'required|numeric|exists:message_templates,id',
            'language_id' => 'required|numeric|exists:languages,id',
            'text' => 'required|max:10000'
        );

        $messages = array(
            'message_template_id.exists' => 'Given message template does not exist in DB',
            'language_id.exists' => 'Given language does not exist in DB'
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
