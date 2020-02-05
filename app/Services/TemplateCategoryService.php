<?php

namespace App\Services;

use App\Models\TemplateCategory;
use Illuminate\Validation\ValidationException;

class TemplateCategoryService
{
    public static function create(TemplateCategory $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function update(TemplateCategory $item) {

        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}