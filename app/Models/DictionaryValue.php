<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;
use App\Models\Dictionary;


class DictionaryValue extends Eloquent
{
    protected $fillable = [
        'description',
        'name',
        'dictionary_id',
        'order'
    ];

    public function dictionary()
    {
        return Dictionary::find($this->dictionary_id);
    }

    public function getValidator() {
        $data = array(
            'description' => $this->description,
            'name' => $this->name,
            'dictionary_id' => $this->dictionary_id,
            'order' => $this->order
        );

        $rules = array(
            'name' => 'required|max:255',
            'description' => 'required',
            'dictionary_id' => 'required|exists:dictionaries,id',
            'order' => 'required|integer'
        );

        $messages = array(
            'dictionary_id.exists' => 'Dictionary with id ' . $this->dictionary_id . ' does not exist'
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
