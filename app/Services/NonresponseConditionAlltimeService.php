<?php

namespace App\Services;

use App\Models\NonresponseConditionAlltime;
use Illuminate\Validation\ValidationException;

class NonresponseConditionAlltimeService
{
    public static function create(NonresponseConditionAlltime $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(NonresponseConditionAlltime $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}