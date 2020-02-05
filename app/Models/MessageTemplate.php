<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;

/**
 * Class MessageTemplate
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $variable_name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $message_template_contents
 *
 * @package App\Models
 */
class MessageTemplate extends Eloquent
{
	protected $fillable = [
		'variable_name',
        'template_category_id'
	];

	public function message_template_contents()
	{
		return $this->hasMany(\App\Models\MessageTemplateContent::class);
	}

	public function template_category() {
	    return $this->belongsTo(\App\Models\TemplateCategory::class);
    }

    public function getValidator() {
        $data = array(
            'variable_name' => $this->variable_name,
            'template_category_id' => $this->template_category_id
        );

        $rules = array(
            'variable_name' => 'required|max:100',
            'template_category_id' => 'required|exists:template_categories,id'
        );

        $messages = array(
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
