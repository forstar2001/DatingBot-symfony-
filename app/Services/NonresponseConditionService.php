<?php

namespace App\Services;

use App\Models\NonresponseCondition;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NonresponseConditionService
{
    public static function create(NonresponseCondition $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(NonresponseCondition $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}