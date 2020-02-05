<?php

namespace App\Services;

use App\Models\DictionaryValue;
use App\Models\Dictionary;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class DictionaryValueService
{
    public static function createDictionaryValue(DictionaryValue $value) {
        $validator = $value->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $tablename = Dictionary::findOrFail($value->dictionary_id)->tablename;

        return DB::table(config('database.dictionary_prefix') . $tablename)
            ->insertGetId(
                ['name' => $value->name, 'description' => $value->description,
                 'order' => $value->order]
            );
    }

    public static function updateDictionaryValue(DictionaryValue $value) {
        $validator = $value->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $tablename = Dictionary::findOrFail($value->dictionary_id)->tablename;

        DB::table(config('database.dictionary_prefix') . $tablename)
            ->where('id', $value->id)
            ->update([
                'name' => $value->name, 'description' => $value->description,
                'order' => $value->order
            ]);
    }

    public static function removeDictionaryValue(DictionaryValue $value) {

        $validator = $value->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $tablename = Dictionary::findOrFail($value->dictionary_id)->tablename;

        DB::table(config('database.dictionary_prefix') . $tablename)
            ->where('id', $value->id)
            ->delete();
    }

    public static function getDictionaryValue($id, $dictionaryId) {
        $tablename = Dictionary::findOrFail($dictionaryId);

        return DB::table(config('database.dictionary_prefix') . $tablename)
            ->where('id', $id)
            ->firstOrFail();

    }

    public static function getDictionaryValues($dictionaryId, $pageNumber, $pageSize) {

        $tablename = Dictionary::findOrFail($dictionaryId)->tablename;


        return DB::table(config('database.dictionary_prefix') . $tablename)
            ->orderBy('order', 'asc')
            ->skip(($pageNumber - 1) * $pageSize)
            ->take($pageSize)
            ->get();
    }


}