<?php


namespace App\Http\Controllers;


use App\Jobs\CheckLink;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\SourceBotProfileStatuses;
use App\Models\SourcesBotProfiles;
use App\Services\CheckLinkService;
use App\Services\RoleCheckingHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SourcesBotProfilesController extends Controller
{
    private $category = 'Sources BotProfiles';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';
    /**
     * @var CheckLinkService
     */
    private $checkLinkService;

    public function __construct(CheckLinkService $checkLinkService)
    {

        $this->checkLinkService = $checkLinkService;
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
            /**
             * @var $sourcesBotProfiles SourcesBotProfiles
             */
            $sourcesBotProfiles = SourcesBotProfiles::find($request->id);
            if ($sourcesBotProfiles === null) {
                $result['success'] = false;
                $result['errors'] = array("SourcesBotProfiles with id $request->id not found");
                return json_encode($result);
            }
            if(isset($request->link)){
                $sourcesBotProfiles->link = $request->link;
            }
            if(isset($request->status)){
                $sourcesBotProfiles->status_id = $request->status;
            }
            if(isset($request->last_check_date)){
                $sourcesBotProfiles->last_check_date = $request->last_check_date;
            }

            if (!RoleCheckingHelper::checkIfActionAuthorized($sourcesBotProfiles,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return new Response('Action is not authorized', 403);
            }

            $sourcesBotProfiles->save();

            if(isset($request->link)){
                $this->checkLinkService->check($sourcesBotProfiles);
            }

            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getAll()
    {
        try{
            $result['success'] = true;
            if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionView)->first())) {
                return new Response('Action is not authorized', 403);
            }
            $statusActiveId = SourceBotProfileStatuses::where('status', SourceBotProfileStatuses::statusActive)
                ->first()->id;
            $statusBrokenId = SourceBotProfileStatuses::where('status', SourceBotProfileStatuses::statusBroken)
                ->first()->id;
            $sourcesBotProfiles = SourcesBotProfiles::whereIn('status_id', [$statusActiveId, $statusBrokenId])
                ->where('link', '<>', '')
                ->get();
            $result['data'] = $sourcesBotProfiles;
            return json_encode($result);
        }catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
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

        $parentId = null;
        $statusId = null;
        $countryId = null;
        $regionId = null;
        if (isset($request->parent_id)) {
            $parentId = $request->parent_id;
        }
        if (isset($request->status_id)) {
            $statusId = $request->status_id;
        }
        if (isset($request->country_id)) {
            $countryId = $request->country_id;
        }
        if (isset($request->region_id)) {
            $regionId = $request->region_id;
        }


        if ($result['success'] === true) {
            try {
                $data = SourcesBotProfiles::with(['status:id,status as name', 'botProfile', 'botProfile.country', 'botProfile.region']);
                $data = $data->where(function ($q) use ($parentId, $statusId, $countryId, $regionId) {
                    if ($parentId > 0) {
                        $q->where('source_id', $parentId);
                    }
                    if ($statusId !== null) {
                        $statuses = explode(',', $statusId);
                        $q->whereIn('status_id', $statuses);
                    }
                    if ($countryId !== null) {
                        $q->whereHas('botProfile', function ($q) use ($countryId) {
                            $countryId = explode(',', $countryId);
                            $q->whereIn('country_id', $countryId);
                        });
                    }
                    if ($regionId !== null) {
                        $q->whereHas('botProfile', function ($q) use ($regionId) {
                            $regionId = explode(',', $regionId);
                            $q->whereIn('region_id', $regionId);
                        });
                    }
                });

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first());
                $count = $data->count();

                $data = $data
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
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