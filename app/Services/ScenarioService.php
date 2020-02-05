<?php

namespace App\Services;

use App\Models\Scenario;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ScenarioService
{
    public static function createScenario(Scenario $scenario) {
        Log::info('Create the scenario.', compact('scenario'));
        $validator = $scenario->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $scenario->save();
    }

    public static function updateScenario(Scenario $scenario) {
        Log::info('Update the scenario.', compact('scenario'));
        $validator = $scenario->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $scenario->save();
    }

}