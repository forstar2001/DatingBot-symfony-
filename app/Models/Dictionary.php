<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 06 Sep 2017 09:22:10 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Class MessageTemplate
 *
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $tablename
 * @property string $name
 *
 * @property \Illuminate\Database\Eloquent\Collection $profile_detail_types
 *
 * @package App\Models
 */
class Dictionary extends Eloquent
{
    protected $fillable = [
        'tablename',
        'name'
    ];

    protected $appends = [
        'dictionary_values'
    ];

    private $languageDictionaryId = 6;

    protected function getDictionaryValuesAttribute()
    {
        if ($this->id == $this->languageDictionaryId) {
            return  app('App\Http\Controllers\LanguageController')->getAsCollection();
        } else {
            return DB::table(config('database.dictionary_prefix') . $this->tablename)->orderBy('order')->get();
        }
    }

    public function getValidator() {
        $data = array(
            'tablename' => $this->tablename,
            'name' => $this->name
        );

        $rules = array(
            'tablename' => 'required|max:100',
            'name' => 'required|max:255'
        );

        $messages = array(
        );

        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }
}
