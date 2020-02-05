<?php

namespace App\Services;

use App\Models\Region;
use Illuminate\Validation\ValidationException;

class RegionService
{
    public static function create(Region $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(Region $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}