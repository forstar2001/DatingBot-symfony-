<?php

namespace App\Services;


use App\User;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    public static function update(User $user) {

        $validator = $user->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $user->save();
    }

    public static function create(User $user) {

        $validator = $user->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $user->save();
    }
}