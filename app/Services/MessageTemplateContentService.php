<?php

namespace App\Services;

use App\Models\MessageTemplateContent;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MessageTemplateContentService
{
    public static function createMessageTemplateContent(MessageTemplateContent $messageTemplateContent) {
        $validator = $messageTemplateContent->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $messageTemplateContent->save();
    }

    public static function updateMessageTemplateContent(MessageTemplateContent $messageTemplateContent) {
        $validator = $messageTemplateContent->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $messageTemplateContent->save();
    }


}