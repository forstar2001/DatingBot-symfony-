<?php

namespace App\Http\Controllers;

use App\Models\RuleCaptureRegexp;
use App\Services\RuleCaptureRegexpService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\City;
use App\Models\Rule;
use App\Services\CityService;
use Illuminate\Validation\ValidationException;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Country;
use App\Models\Region;


class RuleCaptureRegexpController extends Controller
{
    private $category = 'Rule capture regexps';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        try {
            $scenario = Rule::with('scenario')
                ->findOrFail($request->rule_id)->scenario;
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $item = new RuleCaptureRegexp();
        $item->regexp = urldecode($request->regexp);
        $item->rule_id = $request->rule_id;
        $item->profile_detail_type_id = $request->profile_detail_type_id;

        try {
            RuleCaptureRegexpService::create($item);
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

        try {
            $scenario = Rule::with('scenario')
                ->findOrFail($request->rule_id)->scenario;
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionUpdate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        try {

            $item = RuleCaptureRegexp::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("Rule capture regexp with id $request->id not found");
                return json_encode($result);
            }
            $item->regexp = urldecode($request->regexp);
            $item->rule_id = $request->rule_id;
            $item->profile_detail_type_id = $request->profile_detail_type_id;

            RuleCaptureRegexpService::update($item);
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

        $item = RuleCaptureRegexp::find($request->id);

        try {
            $scenario = Rule::with('scenario')
                ->findOrFail($item->rule_id)->scenario;
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Rule capture regexp with id $request->id not found");
            return json_encode($result);
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
                $item = RuleCaptureRegexp::with(['rule', 'rule.scenario', 'profile_detail_type'])
                    ->where('id', $id);

                $item = RoleCheckingHelper::filterDataByViewRoles($item,
                    RoleCategorie::where('name', $this->category)->first(),
                    'scenario.rule')
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

        $result['success'] = true;
        $result['errors'] = array();


        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $parentId = 0;
        if (isset($request->parent_id)) {
            $parentId = $request->parent_id;
        }



        if ($result['success'] == true) {

            try {
                $data = RuleCaptureRegexp::where(function ($q) use ($parentId) {
                    if ($parentId > 0) {
                        $q->where('rule_id', $parentId);
                    }
                })
                    ->with(['rule', 'rule.scenario', 'profile_detail_type']);

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first(),
                    'rule.scenario'
                );

                $count = $data->count();

                if ($request->page > 0 && $request->pagesize > 0) {
                    $result['data'] = $data->skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize)
                        ->get();
                } else {
                    $result['data'] = $data->get();
                }

                $result['total_rows'] = $count;
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
