<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use App\Models\ProfileDetailValueType;
use Illuminate\Support\Facades\Validator;

/**
 * Class ProfileDetailType
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $variable_name
 * @property int $min_value
 * @property int $max_value
 * @property int $max_string_length
 * @property int $profile_detail_value_type_id
 * @property string $regexp
 * 
 * @property \App\Models\ProfileDetailValueType $profile_detail_value_type
 * @property \Illuminate\Database\Eloquent\Collection $bot_profile_details
 * @property \Illuminate\Database\Eloquent\Collection $person_profile_details
 *
 * @package App\Models
 */
class ProfileDetailType extends Eloquent
{
	protected $casts = [
		'min_value' => 'integer|null',
		'max_value' => 'integer|null',
		'max_string_length' => 'integer|null',
		'profile_detail_value_type_id' => 'integer',
        'dictionary_id' => 'integer|null'
	];

	protected $fillable = [
		'name',
		'variable_name',
		'min_value',
		'max_value',
		'max_string_length',
		'profile_detail_value_type_id',
		'regexp',
        'dictionary_id',
        'order'
	];

	public function profile_detail_value_type()
	{
		return $this->belongsTo(\App\Models\ProfileDetailValueType::class);
	}

	public function bot_profile_details()
	{
		return $this->hasMany(\App\Models\BotProfileDetail::class);
	}

	public function person_profile_details()
	{
		return $this->hasMany(\App\Models\PersonProfileDetail::class);
	}

	public function dictionary() {
	    return $this->belongsTo(\App\Models\Dictionary::class);
    }

    public function getValidator() {
        $data = array(
            'name' => $this->name,
            'variable_name' => $this->name,
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
            'max_string_length' => $this->max_string_length,
            'profile_detail_value_type_id' => $this->profile_detail_value_type_id,
            'regexp' => $this->regexp,
            'dictionary_id' => $this->dictionary_id,
            'order' => $this->order
        );

        //if (!empty($this->dictionary_id)) {
        //    array_push($data, $this->dictionary_id);
        //}

        $rules = array(
            'name' => 'required|max:191',
            'variable_name' => 'required|max:50',
            'min_value' => 'numeric|nullable',
            'max_value' => 'numeric|nullable',
            'max_string_length' => 'numeric|nullable',
            'profile_detail_value_type_id' => 'required|exists:profile_detail_value_types,id',
            'regexp' => 'max:191|nullable',
            'dictionary_id' => 'sometimes|exists:dictionaries,id|nullable',
            'order' => 'required|numeric'
        );


        $messages = array(
            'profile_detail_value_type_id.exists' => 'Profile detail value type not found in database',
            'dictionary_id.exists' => 'Dictionary not found in database'
        );

        $validator = Validator::make($data, $rules, $messages);

        $validator->after(function ($validator) {

            $valueType = ProfileDetailValueType::find($this->profile_detail_value_type_id);

            /*if ($valueType == config('database.profile_detail_value_type_string')) {

            } else if ($valueType == config('database.profile_detail_value_type_integer')) {

            } else if ($valueType == config('database.profile_detail_value_type_decimal')) {

            } else if ($valueType == config('database.profile_detail_value_type_datetime')) {

            } else if ($valueType == config('database.profile_detail_value_type_time')) {

            } else if ($valueType == config('database.profile_detail_value_type_image')) {

            } else if ($valueType == config('database.profile_detail_value_type_document')) {

            } else if ($valueType == config('database.profile_detail_value_type_dictionary_country')) {

            } else if ($valueType == config('database.profile_detail_value_type_dictionary_city')) {

            } else if ($valueType == config('database.profile_detail_value_type_dictionary_region')) {

            } */
            if ($valueType == config('database.profile_detail_value_type_dictionary_single')) {
                if (empty($this->dictionary_id)) {
                    $validator->errors()->add('dictionary_id', 'Dictionary must be set for this type of value: ' . $valueType);
                }
            } else if ($valueType == config('database.profile_detail_value_type_dictionary_multiple')) {
                if (empty($this->dictionary_id)) {
                    $validator->errors()->add('dictionary_id', 'Dictionary must be set for this type of value: ' . $valueType);
                }
            }


            /*if (!$this->checkConditionText()) {
                $validator->errors()->add('condition', 'Condition is not valid');
            }
            if (!$this->checkResultMessage()) {
                $validator->errors()->add('result_message', 'Result message is not valid');
            }*/
        });

        return $validator;
    }
}
