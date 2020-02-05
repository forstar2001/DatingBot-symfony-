<?php

namespace App\Services;

use App\Models\BotProfileDetail;
use Illuminate\Validation\ValidationException;

class BotProfileDetailService
{
    public static function createBotProfileDetail(BotProfileDetail $botProfileDetail) {
        $validator = $botProfileDetail->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $botProfileDetail->save();
    }

    public static function updateBotProfileDetail(BotProfileDetail $botProfileDetail) {
        $validator = $botProfileDetail->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $botProfileDetail->save();
    }


}