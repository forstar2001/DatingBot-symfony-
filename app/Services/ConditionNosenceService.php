<?php

namespace App\Services;

use App\Models\ConditionNosence;
use Illuminate\Validation\ValidationException;

class ConditionNosenceService
{
    public static function create(ConditionNosence $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(ConditionNosence $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}