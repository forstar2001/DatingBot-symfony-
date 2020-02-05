<?php

namespace App\Services;

use App\Models\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RuleService
{
    public static function createRule(Rule $rule) {
        Log::info('Create the rule.', compact('rule'));
        $validator = $rule->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $rule->save();
    }

    public static function updateRule(Rule $rule) {
        $validator = $rule->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $rule->save();
    }


}