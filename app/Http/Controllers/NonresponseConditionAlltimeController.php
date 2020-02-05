<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonresponseConditionAlltime;
use App\Services\NonresponseConditionAlltimeService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Rule;
use App\Models\Scenario;


class NonresponseConditionAlltimeController extends Controller
{
    private $category = 'All Time Non Response Conditions';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {

        $item = new NonresponseConditionAlltime();
        $item->order = $request->order;
        $item->result_message = $request->result_message;
        $item->nonresponse_time = $request->nonresponse_time;
        $item->timing_min = $request->timing_min;
        $item->timing_max = $request->timing_max;

        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;

        try {
            NonresponseConditionAlltimeService::create($item);
            $result['item_id'] = $item->id;
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

        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        $item = NonresponseConditionAlltime::find($request->id);
        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Condition with id $request->id not found");
            return json_encode($result);
        }

        $item->order = $request->order;
        $item->result_message = $request->result_message;
        $item->nonresponse_time = $request->nonresponse_time;
        $item->timing_min = $request->timing_min;
        $item->timing_max = $request->timing_max;

        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionUpdate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            NonresponseConditionAlltimeService::update($item);
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

        $item = NonresponseConditionAlltime::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Condition with id $request->id not found");
            return json_encode($result);
        }


        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            $item->delete();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getById(Request $request, $id) {

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;

        if (empty($id)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'id' is required");
        }

        if ($result['success']) {
            try {
                $item = NonresponseConditionAlltime::where('id', $id);

                $item = RoleCheckingHelper::filterDataByViewRoles($item,
                    RoleCategorie::where('name', $this->category)->first())
                    ->first();

                $result['item'] = $item;
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

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;
        $result['errors'] = array();


        if ($result['success'] == true) {
            try {
                $data = NonresponseConditionAlltime::query();

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first()
                );

                if (isset($request->page) && isset($request->pagesize)) {
                    $data = $data->skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize);
                }

                $cnt = NonresponseConditionAlltime::count();

                $result['data'] = $data->get();
                $result['total_rows'] = $cnt;
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
