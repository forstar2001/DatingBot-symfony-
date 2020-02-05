<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public static function create(Role $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(Role $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}