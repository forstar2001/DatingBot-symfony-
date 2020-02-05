<?php

namespace App\Http\Controllers;

use App\Models\BotProfile;
use App\Models\BotProfileDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Services\BotProfileDetailService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class BotProfileDetailController extends Controller
{
    private $category = 'Bot Profile Details';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        try {
            $botProfile = BotProfile::findOrFail($request->bot_profile_id);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($botProfile,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }


        //save to /storage/app/public/bot_profile_documents_$id
        //available from /storage/bot_profile_documents_$id
        if ($request->hasFile('value')) {

            $request->file('value')->storeAs(
                'public/bot_profile_documents_' . $request->bot_profile_id, $request->file('value')->getClientOriginalName()
            );
            $filename = '/bot_profile_documents_' . $request->bot_profile_id . '/' . $request->file('value')->getClientOriginalName();
            $request->value = $filename;
        }

        $botProfileDetail = new BotProfileDetail();
        $botProfileDetail->value = $request->value;
        $botProfileDetail->bot_profile_id = $request->bot_profile_id;
        $botProfileDetail->profile_detail_type_id = $request->profile_detail_type_id;

        try {
            BotProfileDetailService::createBotProfileDetail($botProfileDetail);
            $result['bot_profile_detail_id'] = $botProfileDetail->id;
            $result['item'] = $botProfileDetail;
            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

    public function update(Request $request) {
        $result['success'] = true;


        try {
            $botProfile = BotProfile::findOrFail($request->bot_profile_id);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($botProfile,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionUpdate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        //save to /storage/app/public/bot_profile_documents_$id
        //available from /storage/bot_profile_documents_$id
        if ($request->hasFile('value')) {

            $request->file('value')->storeAs(
                   'public/bot_profile_documents_' . $request->bot_profile_id, $request->file('value')->getClientOriginalName()
               );
            $filename = '/bot_profile_documents_' . $request->bot_profile_id . '/' . $request->file('value')->getClientOriginalName();
            $request->value = $filename;
        }


        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        try {

            $botProfileDetail = BotProfileDetail::find($request->id);
            if ($request->hasFile('value')){
                $pathToImage = storage_path('app/public'.$botProfileDetail->value);
                if(is_file($pathToImage)){
                    unlink($pathToImage);
                }
            }

            if (empty($botProfileDetail)) {
                $result['success'] = false;
                $result['errors'] = array("Bot profile detail with id $request->id not found");
                return json_encode($result);
            }
            $botProfileDetail->value = $request->value;
            $botProfileDetail->bot_profile_id = $request->bot_profile_id;
            $botProfileDetail->profile_detail_type_id = $request->profile_detail_type_id;

            BotProfileDetailService::updateBotProfileDetail($botProfileDetail);
            //$result = $request->file('value')->storeAs('/store/', 'image_1234.png');

            $result['item'] = $botProfileDetail;

            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function delete(Request $request) {
        $result['success'] = true;

        $botProfileDetail = BotProfileDetail::find($request->id);

        try {
            if(preg_match('/bot_profile_documents/',$botProfileDetail->value)){
                $pathToImage = storage_path('app/public'.$botProfileDetail->value);
                if(is_file($pathToImage)){
                    unlink($pathToImage);
                }
            }

            $botProfile = BotProfile::findOrFail($botProfileDetail->bot_profile_id);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($botProfile,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (empty($botProfileDetail)) {
            $result['success'] = false;
            $result['errors'] = array("Bot profile detail with id $request->id not found");
            return json_encode($result);
        }
        try {
            $botProfileDetail->delete();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getById(Request $request, $id) {

        $result['success'] = true;

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (empty($id)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'id' is required");
        }

        if ($result['success']) {
            try {
                $botProfileDetail = BotProfileDetail::with(['bot_profile', 'bot_profile_detail'])
                    ->findOrFail($id);

                $botProfile = BotProfile::where('id', $botProfileDetail->bot_profile_id);

                $botProfile = RoleCheckingHelper::filterDataByViewRoles($botProfile,
                    RoleCategorie::where('name', $this->category)->first())
                    ->first();
                if (!empty($botProfile)) {
                    $result['bot_profile_detail'] = $botProfileDetail;
                } else {
                    $result['bot_profile_detail'] = [];
                }

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

    public function get(Request $request) {

        $result['success'] = true;
        $result['errors'] = array();

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
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

        $botProfileId = 0;
        if (isset($request->parent_id)) {
            $botProfileId = $request->parent_id;
        }

        if ($result['success'] == true) {
            try {
                $data = BotProfileDetail::where(function ($q) use ($botProfileId) {
                        if ($botProfileId > 0) {
                            $q->where('bot_profile_id', $botProfileId);
                        }
                    })
                    ->with(['bot_profile',
                           'profile_detail_type',
                           'profile_detail_type.profile_detail_value_type',
                           'profile_detail_type.dictionary']);
                   // ->get();

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first(),
                    'bot_profile'
                    );

                if ($request->page > 0 && $request->pageSize > 0) {
                    $data = $data->skip(($request->page - 1) * $request->pageSize)
                        ->take($request->pagesize);
                }

                $data = $data->get();


                $result['data'] = $data;
                return json_encode($result);
            }
            catch (\Exception $exception) {
                $result['success'] = false;
                $result['errors'] = array($exception->getMessage());
                return json_encode($result);
            }
        } else {
            return json_encode($result);
        }
    }

}
