<?php

namespace App\Services;

use App\Models\BotProfile;
use App\Models\Source;
use App\Models\SourceBotProfileStatuses;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class BotProfileService
{
    public static function createBotProfile(BotProfile $botProfile)
    {

        $user = Auth::user();

        if (!$user->isAdmin()) {
            $botProfile->status = BotProfile::statusPending;
        }

        $validator = $botProfile->getValidator();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $botProfile->save();
        if ($botProfile->status !== BotProfile::statusApproved) {
            $botProfile->sources()->detach();
        } else {
            $status = SourceBotProfileStatuses::where('status', SourceBotProfileStatuses::statusPending)->first();
            $sources = Source::get();
            foreach ($sources as $source) {
                $botProfile->sources()->attach($source, ['status_id' => $status->id]);
            }
        }
    }

    public static function updateBotProfile(BotProfile $botProfile)
    {

        $user = Auth::user();

        if (!$user->isAdmin()) {
            $botProfile->status = BotProfile::statusPending;
        }

        $validator = $botProfile->getValidator();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($botProfile->status !== BotProfile::statusApproved) {
            $botProfile->sources()->detach();
        } else {
            $status = SourceBotProfileStatuses::where('status', SourceBotProfileStatuses::statusPending)->first();
            $botProfile->sources()->detach();
            $sources = Source::get();
            foreach ($sources as $source) {
                $botProfile->sources()->attach($source, ['status_id' => $status->id]);
            }
        }

        $botProfile->save();
    }
}