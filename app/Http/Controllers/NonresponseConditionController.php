<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonresponseCondition;
use App\Services\NonresponseConditionService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Rule;
use App\Models\Scenario;


class NonresponseConditionController extends Controller
{
    private $category = 'Non-Response Conditions';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {

        try {
            $rule = Rule::findOrFail($request->rule_id);
            $scenario = Scenario::findOrFail($rule->scenario_id);
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

        $result['success'] = true;

        $item = new NonresponseCondition();
        $item->order = $request->order;
        $item->rule_id = $request->rule_id;
        $item->result_message = $request->result_message;
        $item->nonresponse_time = $request->nonresponse_time;
        $item->timing_min = $request->timing_min;
        $item->timing_max = $request->timing_max;

        try {
            NonresponseConditionService::create($item);
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
            $rule = Rule::findOrFail($request->rule_id);
            $scenario = Scenario::findOrFail($rule->scenario_id);
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

            $item = NonresponseCondition::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("Condition with id $request->id not found");
                return json_encode($result);
            }

            $rule = Rule::findOrFail($request->rule_id);
            $scenario = Scenario::findOrFail($rule->scenario_id);

            if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $item->order = $request->order;
            $item->rule_id = $request->rule_id;
            $item->result_message = $request->result_message;
            $item->nonresponse_time = $request->nonresponse_time;
            $item->timing_min = $request->timing_min;
            $item->timing_max = $request->timing_max;

            NonresponseConditionService::update($item);
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

        $item = NonresponseCondition::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Condition with id $request->id not found");
            return json_encode($result);
        }

        try {
            $rule = Rule::findOrFail($item->rule_id);
            $scenario = Scenario::findOrFail($rule->scenario_id);
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
                $item = NonresponseCondition::with(['rule', 'rule.scenario'])
                    ->where('id', $id);

                $item = RoleCheckingHelper::filterDataByViewRoles($item,
                    RoleCategorie::where('name', $this->category)->first(),
                    'rule.scenario')
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

        if (!isset($request->page)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'page' is required");
        }

        if (!isset($request->pagesize)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'pagesize' is required");
        }

        $ruleId = 0;
        if (isset($request->parent_id)) {
            $ruleId = $request->parent_id;
        }

        if ($result['success'] == true) {
            try {
                $data = NonresponseCondition::where(function($q) use ($ruleId) {
                    if ($ruleId > 0) {
                        $q->where('rule_id', $ruleId);
                    }
                })->with(['rule', 'rule.scenario']);
                    //->skip(($request->page - 1) * $request->pageSize)
                    //->take($request->pagesize)
                    //->get();

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first(),
                    'rule.scenario'
                );

                $result['data'] = $data
                    ->skip(($request->page - 1) * $request->pageSize)
                    ->take($request->pagesize)
                    ->get();
                $result['total_rows'] = $data->count();
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
