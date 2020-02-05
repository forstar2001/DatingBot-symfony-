<?php

namespace App\Services;

use App\Models\BotProfile;
use App\Models\Source;
use App\Models\SourceBotProfileStatuses;
use Illuminate\Validation\ValidationException;

class SourceService
{
    public static function create(Source $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }
        $item->save();
        $status = SourceBotProfileStatuses::where('status', SourceBotProfileStatuses::statusPending)->first();
        $botProfiles = BotProfile::where('status', BotProfile::statusApproved)->get();
        foreach ($botProfiles as $botProfile) {
            $item->botProfiles()->attach($botProfile, ['status_id' => $status->id]);
        }

    }

    public static function update(Source $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }


}