<?php

namespace App\Services;

use App\Models\RoleDefinition;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RoleDefinitionService
{
    public static function create(RoleDefinition $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(RoleDefinition $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}