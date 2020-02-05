<?php

namespace App\Services;

use App\Models\RuleCaptureRegexp;
use Illuminate\Validation\ValidationException;

class RuleCaptureRegexpService
{
    public static function create(RuleCaptureRegexp $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(RuleCaptureRegexp $item) {

        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}