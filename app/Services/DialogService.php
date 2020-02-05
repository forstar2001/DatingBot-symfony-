<?php

namespace App\Services;

use App\Models\BotProfile;
use App\Models\BotProfileDetail;
use App\Models\ConditionAlltime;
use App\Models\ConditionNosence;
use App\Models\DialogParticipant;
use App\Models\NonresponseConditionAlltime;
use App\Models\PersonProfile;
use App\Models\PersonProfileDetail;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Models\ProfileDetailType;
use App\Models\Scenario;
use App\Models\TemplateCategory;
use Faker\Provider\ar_JO\Person;
use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;


class DialogService
{
    public static function createDialog(Dialog $dialog) {
        Log::info('Create the dialog.', compact('dialog'));
        $validator = $dialog->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $dialog->save();
    }

    public static function createDialogParticipants(DialogParticipant $item) {
        $validator = $item->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $item->save();
    }

    public static function checkIfNeedToWriteFirst($scenarioId, $dialog) {

        $scenario = Scenario::with(['rules', 'rules.conditions'])->findOrFail($scenarioId);

        if ($scenario->type_id == config('database.scenario_type_write_first_id')) {
            $rule = $scenario->rules->sortBy('order')->first();
            $statement = self::buildIfElseStatement($rule, $dialog);
            $messages = self::createMessage($rule, $dialog, $statement);

            return $messages;
        } else {
            $dialog->current_step = -1;
            return false;
        }

    }

    private static function getStepMessage($dialog, $stepNumber, $messageText) {
        $scenario = $dialog->scenario;//->with(['rules', 'rules.conditions']);
        $rules = array_values($scenario->rules->sortBy('order')->all());
        $currentRule = $rules[$stepNumber];
        //try to capture data
        self::captureDataByRule($currentRule, $dialog, $messageText);
        $statement = self::buildIfElseStatement($currentRule, $dialog);
        $messages = self::createMessage($currentRule, $dialog, $statement);

        return $messages;
    }

    private static function captureDataByRule($rule, $dialog, $messageText) {
        $captureRegexps = $rule->capture_regexps;

        if (!empty($captureRegexps)) {
            foreach ($captureRegexps as $regexp) {
                $finalRegexp = self::replaceVariables($regexp->regexp, $dialog);
                preg_match($finalRegexp, $messageText, $matches);
                if (count($matches) > 0) {
                    $detailTypeId = $regexp->profile_detail_type_id;
                    $detailValue = $matches[1];


                    $detail = PersonProfileDetail
                                ::where('person_profile_id', $dialog->dialog_participants->first()->person_profile_id)
                                ->where('profile_detail_type_id', $detailTypeId)->first();
                    if (empty($detail->id)) {
                        $detail = new PersonProfileDetail();
                        $detail->value = $detailValue;
                        $detail->profile_detail_type_id = $detailTypeId;
                        $detail->person_profile_id = $dialog->dialog_participants->first()->person_profile_id;
                        self::setPersonDetails($detail);
                    }
                }
            }
        }
    }

    private static function buildIfElseStatement($rule, $dialog) {
        $ifElseStatement = "";
        foreach ($rule->conditions as $index => $value) {
            if ($value['condition_type_id'] == 1) {
                $ifElseStatement .= " if ( $value->condition ) { return \"$value->result_message\"; }";
            } else if ($value['condition_type_id'] == 2) {
                $ifElseStatement .= " else if ( $value->condition ) { return \"$value->result_message\"; }";
            }
        }

        $ifElseStatement .= "else { " . self::buildIfElseStatementAllTimeConditions($dialog) ." }";

        return self::replaceVariables($ifElseStatement, $dialog);

    }

    private static function buildIfElseStatementAllTimeConditions($dialog) {
        $ifElseStatement = "";
        foreach (ConditionAlltime::all()->sortBy('order') as $index => $value) {
            if ($index == 0) {
                $ifElseStatement .= " if ( $value->condition ) { return \"$value->result_message\"; }";
            } else {
                $ifElseStatement .= " else if ( $value->condition ) { return \"$value->result_message\"; }";
            }
        }

        $ifElseStatement .= "else { " . self::buildIfElseStatementNoSenceConditions($dialog) ." }";

        return $ifElseStatement;
    }

    private static function buildIfElseStatementNoSenceConditions($dialog) {
        $ifElseStatement = "";
        foreach (ConditionNosence::all()->sortBy('order') as $index => $value) {
            if ($index == 0) {
                $ifElseStatement .= " if ( $value->condition ) { return \"$value->result_message\"; }";
            } else {
                $ifElseStatement .= " else if ( $value->condition ) { return \"$value->result_message\"; }";
            }
        }

        $ifElseStatement .= "else { return \"" . self::getDefaultMessage($dialog) ."\"; }";

        return $ifElseStatement;
    }

    private static function getDefaultMessage($dialog) {
        $category = TemplateCategory::find(config('database.template_category_default_message_id'));
        $templates = MessageTemplate::where('template_category_id', config('database.template_category_default_message_id') )
                        ->get();

        $randomTemplate = $templates[mt_rand(0, count($templates) - 1)];
        return self::getVariableValue('template.' . $category->variable_name . "." . $randomTemplate->variable_name, $dialog);


    }

    private static function createMessage($rule, $dialog, $ifElseStatement) {

        //$ifElseStatement = str_replace(["'"], "", $ifElseStatement);
        //var_dump($ifElseStatement);
        //return;
        //var_dump($ifElseStatement);
        //return;
        $messageText = eval($ifElseStatement);
        $messagesTextArr = explode('|', $messageText);
        $messages = [];

        foreach ($messagesTextArr as $messageText) {

            $message = new Message();
            $message->sender_id = $dialog->dialog_participants->first()->bot_profile_id;
            $message->receiver_id = $dialog->dialog_participants->first()->person_profile_id;

            $message->message_text = $messageText;
            $date = new \DateTime();
            $delay = mt_rand($rule->conditions[0]->timing_min, $rule->conditions[0]->timing_max);
            $typingDelay = round(strlen($message->message_text) / 7);
            $overallDelay = $delay + $typingDelay;
            $date->add(new \DateInterval('PT' . $overallDelay . 'S'));
            $message->datetime_to_send = $date;
            $message->dialog_id = $dialog->id;
            $message->delay = $delay;
            $message->typing_delay = $typingDelay;
            $message->save();

            array_push($messages, $message);
        }

        return $messages;
    }

    public static function setBot(BotProfile $botProfile) {
        Log::info('Setting up the bot', compact('botProfile'));
        $validator = $botProfile->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $botProfile->save();
    }

    public static function setPerson(PersonProfile $personProfile) {
        Log::info('Setting up the person', compact('personProfile'));
        $validator = $personProfile->getValidator();

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $personProfile->save();
    }

    public static function setBotDetails(BotProfileDetail $botDetail) {
        Log::info('setting up bot profile detail', compact('botDetail'));
        $validator = $botDetail->getValidator();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

    }

    public static function setPersonDetails(PersonProfileDetail $personDetail) {
        Log::info('setting up person profile detail', compact('personDetail'));
        $validator = $personDetail->getValidator();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $personDetail->save();

    }


    public static function setOutgoingMessageAsSent($messageId) {
        //Log::info('Set outgoing message status as sent', $messageId);

        $message = Message::findOrFail($messageId);
        $message->is_sent = 1;
        $message->datetime_to_send = Carbon::now();
        $message->push();
    }

    public static function getNonResponseMessages($dialogId) {
        $messages = [];

        $dialog = Dialog::findOrFail($dialogId);

        $scenario = $dialog->scenario;

        if ($dialog->current_step > -1) {
            $currentRule = $scenario->rules->sortBy('order')->all()[$dialog->current_step];
            $nonResponseConditions = $currentRule->nonresponse_conditions->sortBy('order')->all();
            if (count($nonResponseConditions) == 0) {
                $nonResponseConditions = NonresponseConditionAlltime::all()->sortBy('order');
            }
            //print_r(count($nonResponseConditions));

            $previousDelay = 0;
            foreach ($nonResponseConditions as $item) {
                $message = new Message();
                $message->dialog_id = $dialog->id;
                $message->sender_id = $dialog->dialog_participants->first()->bot_profile_id;
                $message->receiver_id = $dialog->dialog_participants->first()->person_profile_id;
                $message->datetime_to_send = NULL;
                $message->message_text = self::replaceVariables($item->result_message, $dialog);
                $message->delay = $previousDelay + $item->nonresponse_time + mt_rand($item->timing_min, $item->timing_max);
                $previousDelay += $message->delay;
                $message->typing_delay = strlen($message->message_text) / 4;
                $message->save();
                array_push($messages, $message);
            }

            if (count($messages) == 0) {
                $nonResponseConditions = NonresponseConditionAlltime::orderBy('order')->all();
            }
        }

        return $messages;
    }


    public static function sendIncomingMessage($dialogId, $messageText, $causeNextStep) {

        $dialog = Dialog::findOrFail($dialogId);
        $senderId = $dialog->dialog_participants->first()->person_profile_id;
        $receiverId = $dialog->dialog_participants->first()->bot_profile_id;
        $lastStep = false;


        //trash, do not calculate the step based on this message
        //but save it to db anyway to see a whole dialog
        if (!$causeNextStep) {
            $message = new Message();
            $message->dialog_id = $dialogId;
            $message->message_text = $messageText;
            $message->datetime_to_send = Carbon::now();
            $message->sender_id = $senderId;
            $message->receiver_id = $receiverId;
        } else {
            $message = new Message();
            $message->dialog_id = $dialogId;
            $message->message_text = $messageText;
            $message->datetime_to_send = Carbon::now();
            $message->sender_id = $senderId;
            $message->receiver_id = $receiverId;
        }

        $dialog->current_step++;
        $rulesCnt = $dialog->scenario->rules->count();
        if ($dialog->current_step + 1 >= $rulesCnt) {
            $lastStep = true;
        } else {
            if ($causeNextStep) {
                $dialog->save();
            }
        }

        $message->save();
        if ($causeNextStep) {
            //we need to get new message
            $newMessages = self::getStepMessage($dialog, $dialog->current_step, $messageText);
            $result['messages'] = $newMessages;
        }

        $result['last_step'] = $lastStep;

        return $result;

    }

    public static function getOutgoingMessage($dialogId)  {
        //Log::info('Get outgoing message', $dialogId);
        $dialog = Dialog::findOrFail($dialogId);
        $firstNotSentMessage = $dialog->messages->filter(function ($item) {
            return $item->is_sent == false && $item->datetime_to_send != NULL;
        })->sortBy('id')->first();
        if (!$firstNotSentMessage) {
           return false;
        }
        return $firstNotSentMessage;
    }

    public static function calculateStep(Dialog $dialog, $stepNumber, $substepNumber) {

        $personProfile = PersonProfile::findOrFail($dialog->dialog_participants->first()->person_profile_id);
        $personTimezone = $personProfile->person_profile_details->filter(function($item) {
            return $item->profile_detail_type->variable_name == "timezone";
        })->first()->value;
        date_default_timezone_set($personTimezone);

        $ifElseStatement = "";
        //var_dump($dialog->scenario->rules->count());
        if ($dialog->scenario->rules->count() < ($stepNumber + 1)) {
            //finish of the script
            return -1;
        }
        $rule = $dialog->scenario->rules[$stepNumber];
        $conditions = $rule->conditions->sortBy('order')->all();

        //todo: non response step
        if ($stepNumber == 0) {
            //respond to message
            if ($substepNumber == 0) {
                if ($dialog->scenario->type_id == 1) {
                    //find the message
                    $message = $dialog->messages->sortBy('id')->first();

                    if (!$message) {
                        return;
                    }

                    foreach ($conditions as $index => $value) {
                        if ($value['condition_type_id'] == 1) {
                            $ifElseStatement .= " if ( $value->condition ) { return '$value->result_message'; }";
                        } else if ($value['condition_type_id'] == 2) {
                            $ifElseStatement .= " else if ( $value->condition ) { return '$value->result_message'; }";
                        } else {
                            $ifElseStatement .= " else { return '$value->result_message'; }";
                        }
                    }

                    $ifElseStatement = self::replaceVariables($ifElseStatement, $dialog, $stepNumber);
                    //var_dump($ifElseStatement);
                    //return;
                } else {
                    foreach ($conditions as $index => $value) {
                        if ($value['condition_type_id'] == 1) {
                            $ifElseStatement .= " if ( $value->condition ) { return '$value->result_message'; }";
                        } else if ($value['condition_type_id'] == 2) {
                            $ifElseStatement .= " else if ( $value->condition ) { return '$value->result_message'; }";
                        } else {
                            $ifElseStatement .= " else { return '$value->result_message'; }";
                        }
                    }

                    $ifElseStatement = self::replaceVariables($ifElseStatement, $dialog, $stepNumber);
                }
            } else {

                $personId = $dialog->dialog_participants->first()->person_profile_id;
                $lastOutgoingMsg = $dialog->messages->filter(function($item) use ($personId) {
                    return $item->receiver_id == $personId && $item->is_sent === 1;
                })->last();

                //we don't have incoming message since last step
                if ($lastOutgoingMsg) {
                    if ($dialog->messages->last()->id
                        == $lastOutgoingMsg->id
                    ) {

                        //check nonresponse conditions

                        $nonResponseCondition = $dialog->scenario->rules[$stepNumber]->nonresponse_conditions[$substepNumber - 1];
                        $dateTimeDiff = //date_diff(
                            strtotime((new \DateTime())->format('Y-m-d H:i:s')) -
                            strtotime($lastOutgoingMsg->datetime_to_send);
                        //);
                        //return $dateTimeDiff;
                        //person didn't respond for x seconds
                        if ($dateTimeDiff > $nonResponseCondition->nonresponse_time) {
                            $messageText = self::replaceVariables($nonResponseCondition->result_message, $dialog, $stepNumber);
                            $message = new Message();
                            $message->sender_id = $dialog->dialog_participants->first()->bot_profile_id;
                            $message->receiver_id = $dialog->dialog_participants->first()->person_profile_id;
                            $message->message_text = $messageText;
                            $date = new \DateTime();
                            $seconds = mt_rand($nonResponseCondition->timing_min, $nonResponseCondition->timing_max);
                            $date->add(new \DateInterval('PT' . $seconds . 'S'));
                            $message->datetime_to_send = $date;
                            $message->dialog_id = $dialog->id;
                            $message->save();

                            return;
                        }

                    }
                }

                return -2;
            }
        } else {

            $personId = $dialog->dialog_participants->first()->person_profile_id;
            $lastOutgoingMsg = $dialog->messages->filter(function($item) use ($personId) {
                return $item->receiver_id == $personId && $item->is_sent === 1;
            })->last();

            if ($substepNumber > 0) {
                //we don't have incoming message since last step
                if ($lastOutgoingMsg) {
                    if ($dialog->messages->last()->id == $lastOutgoingMsg->id) {

                        //check nonresponse conditions

                        $nonResponseCondition = $dialog->scenario->rules[$stepNumber]->nonresponse_conditions[$substepNumber - 1];
                        $dateTimeDiff = //date_diff(
                            strtotime((new \DateTime())->format('Y-m-d H:i:s')) -
                            strtotime($lastOutgoingMsg->datetime_to_send);
                        //);
                        //person didn't respond for x seconds
                        if ($dateTimeDiff > $nonResponseCondition->nonresponse_time) {
                            $messageText = self::replaceVariables($nonResponseCondition->result_message, $dialog, $stepNumber);
                            $message = new Message();
                            $message->sender_id = $dialog->dialog_participants->first()->bot_profile_id;
                            $message->receiver_id = $dialog->dialog_participants->first()->person_profile_id;
                            $message->message_text = $messageText;
                            $date = new \DateTime();
                            $seconds = mt_rand($nonResponseCondition->timing_min, $nonResponseCondition->timing_max);
                            $date->add(new \DateInterval('PT' . $seconds . 'S'));
                            $message->datetime_to_send = $date;
                            $message->dialog_id = $dialog->id;

                            if ($dialog->scenario->rules->count() == ($stepNumber + 1)
                                && ($dialog->scenario->rules[$stepNumber]->conditions->count() +
                                    $dialog->scenario->rules[$stepNumber]->nonresponse_conditions->count())
                                == ($substepNumber + 1)) {
                                //it is time to drop link
                                $message->drop_link = true;
                            }

                            $message->save();

                            return;
                        }

                    }
                }

                return -2;
            }
            else {

                //if ($dialog->scenario->type_id == 1) {
                    //find the message
                    $message = $dialog->messages->sortBy('id')->first();

                    $ifElseStatement = "";
                    foreach ($conditions as $index => $value) {
                        if ($value->condition_type_id = 1) {
                            $ifElseStatement .= " if ( $value->condition ) { return '$value->result_message'; }";
                        } else if ($value->condition_type_id = 2) {
                            $ifElseStatement .= " else if ( $value->condition ) { return '$value->result_message'; }";
                        } else {
                            $ifElseStatement .= " else { return '$value->result_message'; }";
                        }
                    }

                    $ifElseStatement = self::replaceVariables($ifElseStatement, $dialog, $stepNumber);

                //} else {
                //}
            }
        }

        //$result['message'] = eval($ifElseStatement);
        //$result['time'] = mt_rand($conditions->first->timing_min, $conditions->first->timing_max);
        $message = new Message();
        $message->sender_id = $dialog->dialog_participants->first()->bot_profile_id;
        $message->receiver_id = $dialog->dialog_participants->first()->person_profile_id;
        //$ifElseStatement = str_replace(["'"], "", $ifElseStatement);
        //var_dump($ifElseStatement);
        //return;
        //var_dump($ifElseStatement);
        //return;
        $message->message_text = eval($ifElseStatement);
        $date = new \DateTime();
        $seconds = mt_rand($conditions[0]->timing_min, $conditions[0]->timing_max);
        $date->add(new \DateInterval('PT'.$seconds.'S'));
        $message->datetime_to_send = $date;
        $message->dialog_id = $dialog->id;



        if ($dialog->scenario->rules->count() == ($stepNumber + 1)
            && ($dialog->scenario->rules[$stepNumber]->conditions->count() +
                $dialog->scenario->rules[$stepNumber]->nonresponse_conditions->count())
            == ($substepNumber + 1)) {
            //it is time to drop link
            $message->drop_link = true;
        }

        $message->save();


    }

    private static function replaceVariables($code, Dialog $dialog) {
        $matches = array();
        $matchesCount = preg_match_all('/^.*\{#(.*?)#\}.*$/', $code, $matches);
        //var_dump($matches);
        //return;
        while ($matchesCount > 0) {
            foreach ($matches as $index => $value) {
                if ($index == 0) {
                    continue;
                }
                $replace = self::getVariableValue($value[0], $dialog);
                if ($replace !== false) {
                    //return false;
                    $replace = addslashes($replace);
                    $code = str_replace("{#$value[0]#}", $replace, $code);
                } else {
                    $code = str_replace("{#$value[0]#}", $value[0], $code);
                }
            }
            $matchesCount = preg_match_all('/^.*\{#(.*?)#\}.*$/', $code, $matches);
        }
        return $code;
    }

    private static function getVariableValue($variableName, Dialog $dialog) {
        if (preg_match('/^person\.([a-zA-Z0-9_.]*)/', $variableName, $matches) > 0) {
            //going through person attributes
            $personDetails = $dialog->dialog_participants->first()->person_profile->person_profile_details;
            //preg_match('/^person\.([a-zA-Z0-9])*/',$variableName, $matches );
            foreach ($personDetails as $index=>$value) {
                if ($value->profile_detail_type->variable_name == $matches[1]) {
                    if ($value->profile_detail_type->profile_detail_value_type_id == 1)
                        return $value->value;
                    else return $value->value;
                }
            }
        } else if (preg_match('/^bot\.([a-zA-Z0-9_.]*)/', $variableName, $matches) > 0) {
            //going through bot attributes
            $botDetails = $dialog->dialog_participants->first()->bot_profile->bot_profile_details;
            //preg_match('/^bot\.([a-zA-Z0-9])*/',$variableName, $matches );
            foreach ($botDetails as $index=>$value) {
                if ($value->profile_detail_type->variable_name == $matches[1]) {
                    return $value->value;
                }
            }
        } else if (preg_match('/^template\.([a-zA-Z0-9_]+)\.([a-zA-Z0-9_.]+)/', $variableName, $matches) > 0) {
            //going through message templates
            //preg_match('/^template\.[a-zA-Z0-9]*/',$variableName, $matches );
            $templateCategory = TemplateCategory::where('variable_name', $matches[1])->first();
            $messageTemplate = MessageTemplate::where('variable_name', $matches[2])
                ->where('template_category_id', $templateCategory->id)
                ->first();
            $messageContent = $messageTemplate->message_template_contents->where('language_id', $dialog->language_id)->all();
            $messageContent = array_values( $messageContent );
            if (count($messageContent) > 1) {
                //return $messageContent[3]->text;
                $randomIndex = mt_rand(0, count($messageContent) - 1);
                return $messageContent[$randomIndex]->text;
            } else {
                return $messageContent[0]->text;
            }
        } else if (preg_match('/^weather\.([a-zA-Z0-9]*)/', $variableName, $matches) > 0) {
            //detect the weather
            //todo: weather detector
        } else if (preg_match('/^time\.([a-zA-Z0-9]*)/', $variableName, $matches) > 0) {
            //detect current time
            //todo: time detector
            $date = new \DateTime();
            if ($matches[1] == "hour") {
                return $date->format("H");
            }
        } else if (preg_match('/^message\.([a-zA-Z0-9]*)/', $variableName, $matches) > 0) {
            //take message text
            //return $dialog->messages->
            //where('sender_id', '=', $dialog->dialog_participants->person_profile_id)[$stepNumber];
            if ($matches[1] == "text") {
                return $dialog->messages->last()->message_text;
            } else if ($matches[1] == "datetime") {
                return $dialog->messages->last()->created_at;
            }
        } else if (preg_match('/^link/', $variableName, $matches) > 0) {
            return $dialog->source->sourcesBotProfiles
                ->where('bot_profile_id', $dialog->dialog_participants->first()->bot_profile_id)
                ->first()->link;
        }

        //not found
        return false;
    }

    //((\(

    //\))*(or|and)*)+
    //regex for condition
    //(([[a-zA-z].+[a-zA-z]|'{[a-zA-Z}'|'[a-zA-Z]'|true|false !+contains|length|regexp([text|''|'{}'])] =|>|<|!=|>=|<=)[and|or]+)+

    public static function deleteDialog(Dialog $dialog) {



    }

    public static function pickProfileByCountryCity(string $countryToFind, string $regionToFind, string $cityToFind) {

        $profileCityId = config('database.profile_detail_city_id');
        $profileCountryId = config('database.profile_detail_country_id');
        $profileRegionId = config('database.profile_detail_region_id');

        //find profiles with cities
        $botProfiles = BotProfile::with('bot_profile_details')->whereHas('bot_profile_details', function($query) use ($profileCityId){
           $query->where('profile_detail_type_id', $profileCityId);
        })->get();

        $botProfiles = $botProfiles->filter(function($item) use ($cityToFind, $countryToFind, $regionToFind, $profileCityId, $profileCountryId, $profileRegionId) {
            $city = $item->bot_profile_details->where('profile_detail_type_id', $profileCityId)->first();
            $country = $item->bot_profile_details->where('profile_detail_type_id', $profileCountryId)->first();
            $region = $item->bot_profile_details->where('profile_detail_type_id', $profileRegionId)->first();

            if (isset($city) && isset($country) && isset($region) ) {
                return $city->value == $cityToFind && $country->value == $countryToFind && $region->value == $regionToFind;
            } else {
                return false;
            }
        });


        $cnt = $botProfiles->count();

        if ($cnt > 0) {
            $rnd = mt_rand(0, $cnt - 1);
            return $botProfiles[$rnd]->id;
        } else {

            //find profiles with region
            $botProfiles = BotProfile::with('bot_profile_details')->whereHas('bot_profile_details', function($query) use($profileRegionId) {
                $query->where('profile_detail_type_id', $profileRegionId);
            })->get();

            $botProfiles = $botProfiles->filter(function($item) use ($regionToFind, $countryToFind, $profileRegionId, $profileCountryId) {
                $region = $item->bot_profile_details->where('profile_detail_type_id', $profileRegionId)->first();
                $country = $item->bot_profile_details->where('profile_detail_type_id', $profileCountryId)->first();

                if (isset($country) && isset($region)) {
                    return $country->value == $countryToFind && $region->valie == $regionToFind;
                } else {
                    return false;
                }
            });

            $cnt = $botProfiles->count();

            if ($cnt > 0) {
                $rnd = mt_rand(0, $cnt - 1);
                return $botProfiles[$rnd]->id;
            } else {

                //find profiles with country
                $botProfiles = BotProfile::with('bot_profile_details')->whereHas('bot_profile_details', function($query) use($profileCountryId) {
                    $query->where('profile_detail_type_id', $profileCountryId);
                })->get();

                $botProfiles = $botProfiles->filter(function($item) use ($countryToFind, $profileCountryId) {
                    $country = $item->bot_profile_details->where('profile_detail_type_id', $profileCountryId)->first();

                    if (isset($country)) {
                        return $country->value == $countryToFind;
                    } else {
                        return false;
                    }
                });

                $cnt = $botProfiles->count();

                if ($cnt > 0) {
                    $rnd = mt_rand(0, $cnt - 1);
                    return $botProfiles[$rnd]->id;
                }
                else {
                    //just get random profile
                    return BotProfile::all()->random()->id;
                }
            }
        }


    }

}