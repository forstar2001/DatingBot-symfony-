<?php

namespace App\Services;

use App\Models\ProfileDetailType;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProfileDetailTypeService
{
    public static function createProfileDetailType(ProfileDetailType $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function updateProfileDetailType(ProfileDetailType $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}