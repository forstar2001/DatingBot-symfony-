<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;



class TemplateCategory extends Eloquent
{
    protected $fillable = [
        'name',
        'variable_name'
    ];

    public $timestamps = false;

    public function getValidator()
    {
        $data = array(
            'name' => $this->name,
            'variable_name' => $this->variable_name
        );

        $rules = array(
            'name' => 'required|max:255',
            'variable_name' => 'required|max:255|regex:/[a-zA-Z0-9_]+/'
        );

        $messages = array(
            'variable_name.regex' => 'Variable name can contain only symbols: A-Z, a-z, 0-9, _'
        );

        return Validator::make($data, $rules, $messages);
    }
}
