<?php

namespace App\Services;

use App\Models\ConditionAlltime;
use Illuminate\Validation\ValidationException;

class ConditionAlltimeService
{
    public static function create(ConditionAlltime $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(ConditionAlltime $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}