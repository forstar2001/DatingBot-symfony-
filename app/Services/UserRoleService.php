<?php

namespace App\Services;

use App\Models\UserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserRoleService
{
    public static function create(UserRole $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(UserRole $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}