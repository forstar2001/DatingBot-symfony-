<?php

namespace App\Services;

use App\Models\Dictionary;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DictionaryService
{
    public static function createDictionary(Dictionary $dict) {
        $validator = $dict->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        Schema::create(config('database.dictionary_prefix') . $dict->tablename, function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('order')->nullable();
            $table->unique(['name']);
        });

        $dict->save();
    }

    public static function renameDictionary(Dictionary $dict, $currentTablename) {
        $validator = $dict->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        Schema::rename(config('database.dictionary_prefix') . $currentTablename,
            config('database.dictionary_prefix') . $dict->tablename);

        $dict->save();
    }

    public static function removeDictionaryFromDb(Dictionary $dict) {
        Schema::dropIfExists(config('database.dictionary_prefix') . $dict->tablename);
    }


}