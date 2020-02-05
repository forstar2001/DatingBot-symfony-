<?php

namespace App\Services;

use App\Models\Condition;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ConditionService
{
    public static function createCondition(Condition $condition) {
        $validator = $condition->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $condition->save();
    }

    public static function updateCondition(Condition $condition) {
        $validator = $condition->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $condition->save();
    }


}