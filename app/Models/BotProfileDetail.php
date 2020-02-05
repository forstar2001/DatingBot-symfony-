<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;


/**
 * Class BotProfileDetail
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $bot_profile_id
 * @property int $profile_detail_type_id
 * @property string $value
 * 
 * @property \App\Models\BotProfile $bot_profile
 * @property \App\Models\ProfileDetailType $profile_detail_type
 *
 * @package App\Models
 */
class BotProfileDetail extends Eloquent
{
	protected $casts = [
		'bot_profile_id' => 'int',
		'profile_detail_type_id' => 'int'
	];

	protected $fillable = [
		'bot_profile_id',
		'profile_detail_type_id',
		'value'
	];

	protected $appends = [
	    'values_arr',
        'file_path'
    ];

	public function getValuesArrAttribute() {
	    return explode(',', $this->value);
    }

    public function getFilePathAttribute() {
	    if ($this->profile_detail_type->profile_detail_value_type == config('database.profile_detail_value_type_name_image')) {
	        return $this->value;
        }
    }

	public function bot_profile()
	{
		return $this->belongsTo(\App\Models\BotProfile::class);
	}

	public function profile_detail_type()
	{
		return $this->belongsTo(\App\Models\ProfileDetailType::class);
	}

    public function getValidator()
    {
        $data = array(
            'bot_profile_id' => $this->bot_profile_id,
            'profile_detail_type_id' => $this->profile_detail_type_id,
            'value' => $this->value
        );

        $rules = array(
            'bot_profile_id' => 'required|exists:bot_profiles,id',
            'profile_detail_type_id' => 'required|exists:profile_detail_types,id',
            'value' => 'required|max:1000'
        );

        $messages = array(
            'bot_profile_id.exists' => 'Given bot profile id doesn\'t exists in db',
            'profile_detail_type_id' => 'Given profile detail type id doesn\'t exists in DB',
            'value.max' => 'Maximum length of detail value is 1000 characters'
        );

        return Validator::make($data, $rules, $messages);
    }
}
