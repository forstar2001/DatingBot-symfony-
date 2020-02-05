<?php

namespace App\Http\Controllers;


use App\Services\DialogService;
use App\Services\FrontendService;
use Illuminate\Http\Request;
use MaxMind\Db\Reader;


class FrontendController extends Controller {

    public function pickProfile ($country, $region, $city) {

        $result['success'] = true;
        $result['errors'] = [];

        if (!isset($request->country)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "country" is required');
        }

        if (!$result['success']) {
            return json_encode($result);
        }

        try {
            $profileId = DialogService::pickProfileByCountryCity($request->country, $request->region, $request->city);
            $result['success'] = true;
            $result['profile_id'] = $profileId;
            $result['profile_details'] = $this->getBotProfileDetails($profileId);
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function detectGeo(Request $request) {
        $result['success'] = true;
        $result['errors'] = [];

        if (!isset($request->ip)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "ip" is required');
        }

        if (!$result['success']) {
            return json_encode($result);
        }

        $ip = $request->ip;
        $dbFile = app_path('GEO/GeoLite2-City.mmdb');
        $reader = new Reader($dbFile);
        print_r($reader->get($ip));
    }

    public function getInitialDataForProfiles(Request $request) {

        $result['success'] = true;
        $result['errors'] = [];

        if (!isset($request->ip)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "ip" is required');
        }

        if (!isset($request->source_id)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "source_id" is required');
        }

        if (!$result['success']) {
            return json_encode($result);
        }

        $initialData = FrontendService::getInitialDataForProfiles($request->ip, $request->source_id);

        $initialData['success'] = true;
        return json_encode($initialData);
    }

    public function getInitialDataForChat(Request $request) {
        $result['success'] = true;
        $result['errors'] = [];

        if (!isset($request->profile_id)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "profile_id" is required');
        }

        if (!isset($request->person_id)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "person_id" is required');
        }

        if (!isset($request->source_id)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "source_id" is required');
        }

        if (!$result['success']) {
            return json_encode($result);
        }

        $initialData = FrontendService::getInitialDataForChat($request->profile_id, $request->person_id, $request->source_id);
        $dialog = FrontendService::setupDialog($request->profile_id, $request->person_id, $request->source_id);

        $messagesToSend = DialogService::checkIfNeedToWriteFirst($dialog->scenario_id, $dialog);

        if ($messagesToSend) {
            $initialData['messages'] = $messagesToSend;
            $initialData['non_response_messages'] = DialogService::getNonResponseMessages($dialog->id);
        }
        $initialData['success'] = true;
        $initialData['dialog_id'] = $dialog->id;
        return json_encode($initialData);
    }

    public function setOutgoingMessageAsSent(Request $request) {
        $result['success'] = true;
        $result['errors'] = [];

        if (!isset($request->message_id)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], 'Parameter "message_id" is required');
        }

        if (!$result['success']) {
            return json_encode($result);
        }

        DialogService::setOutgoingMessageAsSent($request->message_id);

        return json_encode($result);
    }

    public function sendIncomingMessage(Request $request) {
        $result['success'] = true;
        $result['errors'] = [];

        if (!isset($request->dialog_id)) {
            $result['success'] = false;
            array_push($result['errors'], 'Parameter "dialog_id" is required');
        }
        if (!isset($request->message_text)) {
            $result['success'] = false;
            array_push($result['errors'], 'Parameter "message_text" is required');
        }
        if (!isset($request->cause_next_step)) {
            $result['success'] = false;
            array_push($result['errors'], 'Parameter "cause_next_step" is required');
        }


        if (!$result['success']) {
            return json_encode($result);
        }

        $fncResult = DialogService::sendIncomingMessage($request->dialog_id, $request->message_text, $request->cause_next_step);

        if ($request->cause_next_step) {
            $result['messages'] = $fncResult['messages'];
            $result['non_response_messages'] = DialogService::getNonResponseMessages($request->dialog_id);
        }
        $result['last_step'] = $fncResult['last_step'];

        return json_encode($result);
    }


}