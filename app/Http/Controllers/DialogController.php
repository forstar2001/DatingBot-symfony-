<?php

namespace App\Http\Controllers;

use App\Models\BotProfile;
use App\Models\BotProfileDetail;
use App\Models\City;
use App\Models\PersonProfile;
use App\Models\PersonProfileDetail;
use App\Models\ProfileDetailType;
use App\Models\Dialog;
use App\Models\DialogParticipant;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Services\RoleCheckingHelper;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Services\DialogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use Psy\Exception\ErrorException;

class DialogController extends Controller
{
    private $category = 'Conversations';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request)
    {
        $dialog = new Dialog();
        $dialog->scenario_id = 2; //I have no idea now how to choose scenario//$request->scenario_id;
        $dialog->language_id = 1; //$request->language_id;

        try {
            DialogService::createDialog($dialog);
            $result['success'] = true;
            $result['dialog_id'] = $dialog->id;
            return json_encode($result);
        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function setBot(Request $request)
    {

        if (empty($request->dialog_id)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "dialog_id" is required');
            return json_encode($result);
        }

        try {

            DB::transaction(function () use ($request, &$botProfileId) {
                $dialog = Dialog::findOrFail($request->dialog_id);

                if (!empty($request->bot_profile_id)) {
                    $botProfile = BotProfile::findOrFail($request->bot_profile_id);
                    DialogService::setBot($botProfile);
                } else {
                    $botProfile = new BotProfile();
                    $botProfile->name = $request->name;
                    $botProfile->source_id = $request->source_id;
                    DialogService::setBot($botProfile);
                }

                $dialogParticipants = $dialog->dialog_participants->first();
                if (!$dialogParticipants) {
                    $newDialogParticipants = new DialogParticipant();
                    $newDialogParticipants->bot_profile_id = $botProfile->id;
                    $newDialogParticipants->dialog = $dialog;
                    $newDialogParticipants->save();
                } else {
                    $dialogParticipants->bot_profile_id = $botProfile->id;
                    $dialogParticipants->save();
                }

                $botProfileId = $botProfile->id;

            });
            $result['success'] = true;
            $result['bot_profile_id'] = $botProfileId;
            return json_encode($result);

        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

    public function setPerson(Request $request)
    {

        if (empty($request->dialog_id)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "dialog_id" is required');
            return json_encode($result);
        }

        try {


            DB::transaction(function () use ($request, &$personProfileId) {
                //return json_encode(print_r($id));
                $dialog = Dialog::findOrFail($request->dialog_id);
                $personProfile = new PersonProfile();
                $personProfile->name = $request->name;
                $personProfile->source_id = $request->source_id;
                DialogService::setPerson($personProfile);

                $dialogParticipants = $dialog->dialog_participants->first();
                if (!$dialogParticipants) {
                    $newDialogParticipants = new DialogParticipant();
                    $newDialogParticipants->person_profile_id = $personProfile->id;
                    $newDialogParticipants->dialog_id = $request->dialog_id;
                    $newDialogParticipants->save();
                } else {
                    $dialogParticipants->person_profile_id = $personProfile->id;
                    $dialogParticipants->save();
                }

                $personProfileId = $personProfile->id;


            });

            $result['success'] = true;
            $result['person_profile_id'] = $personProfileId;
            return json_encode($result);


        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

    public function setBotDetails(Request $request)
    {
        if (empty($request->bot_profile_id)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "bot_profile_id" is required');
            return json_encode($result);
        }

        try {
            //return json_encode($request->details);
            DB::transaction(function () use ($request) {
                $botProfile = BotProfile::findOrFail($request->bot_profile_id);
                foreach ($request->details as $index => $value) {
                    $botDetail = new BotProfileDetail();
                    $botDetail->bot_profile_id = $request->bot_profile_id;
                    $botDetail->value = $value['value'];
                    $botDetail->profile_detail_type_id = $value['profile_detail_type_id'];

                    DialogService::setBotDetails($botDetail);
                    $botDetail->save();
                }
            });
            $result['success'] = true;
            return json_encode($result);
        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function setPersonDetails(Request $request)
    {
        if (empty($request->person_profile_id)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "person_profile_id" is required');
            return json_encode($result);
        }

        try {
            DB::transaction(function () use ($request) {
                $personProfile = PersonProfile::findOrFail($request->person_profile_id);
                foreach ($request->details as $index => $value) {
                    $personDetail = new PersonProfileDetail();
                    $personDetail->person_profile_id = $request->person_profile_id;
                    $personDetail->value = $value['value'];
                    $personDetail->profile_detail_type_id = $value['profile_detail_type_id'];

                    DialogService::setPersonDetails($personDetail);
                    $personDetail->save();
                }
            });
            $result['success'] = true;
            return json_encode($result);
        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function saveIncomingMessage(Request $request)
    {
        try {
            $dialog = Dialog::findOrFail($request->dialog_id);

            $dialogParticipants = $dialog->dialog_participants->first();
            if (!$dialogParticipants) {
                throw new \Exception('Dialog doesn\'t have participants');
            }

            //do not increment step number
            if ($dialog->messages->count() > 0) {
                if ($dialog->messages->last()->sender_id === $dialogParticipants->person_profile_id) {
                    $result['same_step'] = true;
                }
            }

            $message = new \App\Models\Message();
            $message->dialog_id = $request->dialog_id;

            $message->sender_id = $dialogParticipants->person_profile_id;
            $message->receiver_id = $dialogParticipants->bot_profile_id;
            $message->message_text = $request->message_text;

            DialogService::saveIncomingMessage($message);
            $result['success'] = true;
            return json_encode($result);
        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function setOutgoingMessageAsSent(Request $request)
    {
        try {
            DialogService::setOutgoingMessageAsSent($request->message_id);
            $result['success'] = true;
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getOutgoingMessage(Request $request)
    {
        try {
            $message = DialogService::getOutgoingMessage($request->dialog_id);
            $result['message'] = $message;
            $result['success'] = true;
            return $result;
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getAll(Request $request)
    {
        return json_encode(Dialog::with(['scenario', 'language', 'dialog_participants'])->get());
    }

    public function calculateStep(Request $request)
    {
        if (!isset($request->step_number)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "step number" is required');
            return json_encode($result);
        }
        if (!isset($request->substep_number)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "substep number" is required');
            return json_encode($result);
        }

        try {
            $dialog = Dialog::findOrFail($request->dialog_id);
            $resultCode = DialogService::calculateStep($dialog, $request->step_number, $request->substep_number);
            if ($resultCode == -1) {
                $result['end'] = true;
            }
            //$result['1'] = $resultCode['1'];
            //$result['2'] = $resultCode['2'];
            $result['code'] = $resultCode;
            $result['success'] = true;
            return json_encode($result);
        } catch (Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage(), $exception->getTraceAsString());
            return json_encode($result);
        }
    }

    public function pickProfileByCountryCity(Request $request)
    {
        if (!isset($request->region)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "region" is required');
            return json_encode($result);
        }
        if (!isset($request->city)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "city" is required');
            return json_encode($result);
        }
        if (!isset($request->country)) {
            $result['success'] = false;
            $result['errors'] = array('Parameter "country" is required');
            return json_encode($result);
        }

        try {
            $profileId = DialogService::pickProfileByCountryCity($request->country, $request->region, $request->city);
            $result['success'] = true;
            $result['profile_id'] = $profileId;
            $result['profile_details'] = $this->getBotProfileDetails($profileId);
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    private function getBotProfileDetails($profileId)
    {
        $botProfile = BotProfile::with('bot_profile_details')->findOrFail($profileId);
        $details = $botProfile->bot_profile_details;
        foreach ($details as $value) {
            $value->profile_detail_type = ProfileDetailType::findOrFail($value->profile_detail_type_id);
        };
        return $details;
    }

    public function get(Request $request)
    {
        $result['success'] = true;
        $result['errors'] = array();

        if (!RoleCheckingHelper::checkIfActionAuthorized(null,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (!isset($request->page)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'page' is required");
        }

        if (!isset($request->pagesize)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'pagesize' is required");
        }

        $scenarioId = $request->scenario_id;
        $countryId = $request->country_id;
        $regionId = $request->region_id;
        $languageId = $request->language_id;
        $sourceId = $request->source_id;
        $botProfileId = $request->bot_profile_id;
        $cityId = $request->city_id;

        if ($result['success'] == true) {
            try {
                $data = Dialog::with([
                    'dialog_participant',
                    'dialog_participant.person_profile',
                    'dialog_participant.person_profile.country',
                    'dialog_participant.bot_profile',
                    'dialog_participant.person_profile.region',
//                    'dialog_participant.person_profile.person_profile_details',
                    'source',
                    'scenario'
                ])
                    ->where(function ($q) use (
                        $scenarioId,
                        $countryId,
                        $regionId,
                        $languageId,
                        $sourceId,
                        $botProfileId,
                        $cityId
                    ) {
                        if ($countryId !== null) {
                            //$countries = explode(',', $countryId);
                            //$q->whereIn('dialog_participant.person_profile.country_id', $countries);
                            $q->whereHas('dialog_participant', function ($q) use ($countryId) {
                                if ($countryId !== null) {
                                    $countryId = explode(',', $countryId);
                                    $q->whereHas('person_profile', function ($q) use ($countryId) {
                                        $q->whereIn('person_profiles.country_id', $countryId);
                                    });
                                }
                            });
                        }
                        if ($regionId !== null) {
                            //$regions = explode(',', $regionId);
                            $q->whereHas('dialog_participant', function ($q) use ($regionId) {
                                if ($regionId !== null) {
                                    $regionId = explode(',', $regionId);
                                    $q->whereHas('person_profile', function ($q) use ($regionId) {
                                        $q->whereIn('person_profiles.region_id', $regionId);
                                    });
                                }
                            });
                        }
                        if ($cityId !== null) {
                            $q->whereHas('dialog_participant', function ($q) use ($cityId) {
                                $cityId = explode(',', $cityId);
                                $q->whereHas('person_profile', function ($q) use ($cityId) {
                                    $q->whereHas('person_profile_details', function ($q) use ($cityId){
                                        $cityNameArr = City::whereIn('id',$cityId)
                                            ->pluck('name')
                                            ->unique()
                                            ->all();
                                        $q->whereIn('value',  $cityNameArr);
                                    });
                                });
                            });
                        }
                        if ($scenarioId !== null) {
                            $scenarios = explode(',', $scenarioId);
                            $q->whereIn('scenario_id', $scenarios);
                        }
                        if ($languageId !== null) {
                            $languages = explode(',', $languageId);
                            $q->whereIn('language_id', $languages);
                        }
                        if ($sourceId !== null) {
                            $sources = explode(',', $sourceId);
                            $q->whereIn('source_id', $sources);
                        }
                        if ($botProfileId !== null) {

                            $botProfiles = explode(',', $botProfileId);
                            $q->whereIn('dialog_participant.bot_profile_id', $botProfiles);
                        }
                    });

                $count = $data->count();
                $data = $data
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->orderBy('id', 'desc')
                    ->get();

                $result['data'] = $data;
                $result['total_rows'] = $count;
                return json_encode($result);
            } catch (\Exception $exception) {
                $result['success'] = false;
                $result['errors'] = array($exception->getMessage());
                return json_encode($result);
            }
        } else {
            return json_encode($result);
        }
    }


}
