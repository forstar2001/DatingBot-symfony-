<?php

namespace App\Services;

use App\Models\MessageTemplate;
use Illuminate\Validation\ValidationException;

class MessageTemplateService
{
    public static function createMessageTemplate(MessageTemplate $messageTemplate) {
        $validator = $messageTemplate->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $messageTemplate->save();
    }

    public static function updateMessageTemplate(MessageTemplate $messageTemplate) {
        $validator = $messageTemplate->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $messageTemplate->save();
    }


}