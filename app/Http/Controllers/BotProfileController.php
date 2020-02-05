<?php

namespace App\Http\Controllers;

use App\Models\BotProfile;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\RoleCategorie;
use App\Models\RoleAction;
use App\Services\BotProfileService;
use App\Services\RoleCheckingHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class BotProfileController extends Controller
{
    private $category = 'Bot Profiles';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request)
    {
        $result['success'] = true;

        $botProfile = new BotProfile();
        $botProfile->name = $request->name;
        $botProfile->country_id = $request->country_id;
        $botProfile->user_id = $request->user_id;
        $botProfile->status = $request->status;
        $botProfile->domain = $request->domain;
        $botProfile->region_id = (int)$request->region_id === 0 ? NULL : $request->region_id;

        if (!RoleCheckingHelper::checkIfActionAuthorized($botProfile,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return new Response('Action is not authorized', 403);
        }

        try {
            BotProfileService::createBotProfile($botProfile);
            $result['bot_profile_id'] = $botProfile->id;
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

    public function update(Request $request)
    {
        $result['success'] = true;

        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        try {

            $botProfile = BotProfile::find($request->id);
            if (empty($botProfile)) {
                $result['success'] = false;
                $result['errors'] = array("Bot profile with id $request->id not found");
                return json_encode($result);
            }
            $botProfile->name = $request->name;
            $botProfile->country_id = $request->country_id;
            $botProfile->region_id = (int)$request->region_id === 0 ? NULL : $request->region_id;
            $botProfile->user_id = $request->user_id;
            $botProfile->status = $request->status;
            $botProfile->domain = $request->domain;


            if (!RoleCheckingHelper::checkIfActionAuthorized($botProfile,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return new Response('Action is not authorized', 403);
            }

            BotProfileService::updateBotProfile($botProfile);
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

    public function delete(Request $request)
    {

        $result['success'] = true;

        $botProfile = BotProfile::find($request->id);

        if (empty($botProfile)) {
            $result['success'] = false;
            $result['errors'] = array("Bot profile with id $request->id not found");
            return json_encode($result);
        }
        try {

            if (!RoleCheckingHelper::checkIfActionAuthorized($botProfile,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionRemove)->first())) {
                return new Response('Action is not authorized', 403);
            }
            $botProfile->sources()->detach();
            $botProfile->delete();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getById(Request $request, $id)
    {

        $result['success'] = true;

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return new Response('Action is not authorized', 403);
        }

        if (empty($id)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'id' is required");
        }

        if ($result['success']) {
            try {
                $botProfile = BotProfile::with(['bot_profile_details'])
                    ->findOrFail($id);

                $botProfile = RoleCheckingHelper::filterDataByViewRoles($botProfile,
                    RoleCategorie::where('name', $this->category)->first())
                    ->first();

                $result['bot_profile'] = $botProfile;
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

    public function get(Request $request)
    {

        $result['success'] = true;

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return new Response('Action is not authorized', 403);
        }

        $result['errors'] = array();

        if (!isset($request->page)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'page' is required");
        }

        if (!isset($request->pagesize)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'pagesize' is required");
        }

        $countryId = null;
        $regionId = null;
        $userId = null;
        $status = null;
        $domain = null;
        $sourcesId = null;

        if (isset($request->country_id)) {
            $countryId = $request->country_id;
        }
        if (isset($request->status)) {
            $status = $request->status;
        }
        if (isset($request->domain)) {
            $domain = $request->domain;
        }
        if (isset($request->region_id)) {
            $regionId = $request->region_id;
        }
        if (isset($request->user_id)) {
            $userId = $request->user_id;
        }
        if (isset($request->sources)) {
            $sourcesId = $request->sources;
        }

        $user = Auth::user();

        if ($result['success'] === true) {
            try {
                $data = BotProfile::with('sources')
                    ->where(function ($q) use ($countryId, $status, $domain, $userId, $regionId, $sourcesId) {
                    if ($countryId !== null) {
                        $countries = explode(',', $countryId);
                        $q->whereIn('country_id', $countries);
                    }
                    if ($regionId !== null) {
                        $regions = explode(',', $regionId);
                        $q->whereIn('region_id', $regions);
                    }
                    if ($status !== null) {
                        $statuses = explode(',', $status);
                        $q->whereIn('status', $statuses);
                    }
                    if ($domain !== null) {
                        $domains = explode(',', $domain);
                        $q->whereIn('domain', $domains);
                    }
                    if ($userId !== null) {
                        $userId = explode(',', $userId);
                        $q->whereIn('user_id', $userId);
                    }
                    if ($sourcesId !== null) {
                        $q->whereHas('sources', function ($q) use ($sourcesId) {
                            if ($sourcesId !== null) {
                                $sourcesId = explode(',', $sourcesId);
                                $q->whereIn('sources.id', $sourcesId);
                            }
                        });
                    }
                });

                $data->get();

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first());

                $count = $data->count();

                $data = $data
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->with([
                        'user',
                        'country',
                        'country.language',
                        'country.regions',
                        'region'
                    ])
                    ->get();

                $result['total_rows'] = $count;
                $result['data'] = $data;
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
