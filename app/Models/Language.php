<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Language
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $code
 * 
 * @property \Illuminate\Database\Eloquent\Collection $dialogs
 * @property \Illuminate\Database\Eloquent\Collection $message_template_contents
 *
 * @package App\Models
 */
class Language extends Eloquent
{
	protected $fillable = [
		'name',
		'code'
	];

	public function dialogs()
	{
		return $this->hasMany(\App\Models\Dialog::class);
	}

	public function message_template_contents()
	{
		return $this->hasMany(\App\Models\MessageTemplateContent::class);
	}

	public function country() {
	    return $this->belongsTo(\App\Models\Country::class, 'id', 'language_id');
    }

}
