<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condition;
use App\Services\ConditionService;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Rule;
use App\Models\Scenario;


class ConditionController extends Controller
{
    private $category = 'Conditions';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
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
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $condition = new Condition();
        $condition->order = $request->order;
        $condition->condition_type_id = $request->condition_type_id;
        $condition->rule_id = $request->rule_id;
        $condition->condition = $request->condition;
        $condition->result_message = $request->result_message;
        $condition->timing_min = $request->timing_min;
        $condition->timing_max = $request->timing_max;

        try {
            ConditionService::createCondition($condition);
            $result['condition_id'] = $condition->id;
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

            $condition = Condition::find($request->id);
            if (empty($condition)) {
                $result['success'] = false;
                $result['errors'] = array("Condition with id $request->id not found");
                return json_encode($result);
            }
            $condition->order = $request->order;
            $condition->condition_type_id = $request->condition_type_id;
            $condition->rule_id = $request->rule_id;
            $condition->condition = $request->condition;
            $condition->result_message = $request->result_message;
            $condition->timing_min = $request->timing_min;
            $condition->timing_max = $request->timing_max;

            ConditionService::updateCondition($condition);
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

        $condition = Condition::find($request->id);


        if (empty($condition)) {
            $result['success'] = false;
            $result['errors'] = array("Condition with id $request->id not found");
            return json_encode($result);
        }

        try {
            $rule = Rule::findOrFail($condition->rule_id);
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
            //$this->authorize('delete', $scenario);

            $condition->delete();
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
                $condition = Condition::with(['rule', 'condition_type', 'rule.scenario'])
                    ->where('id', $id);

                $condition = RoleCheckingHelper::filterDataByViewRoles($condition,
                    RoleCategorie::where('name', $this->category)->first(),
                    'rule.scenario')
                    ->first();

                $result['condition'] = $condition;
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
                $data = Condition::where(function($q) use ($ruleId) {
                    if ($ruleId > 0) {
                        $q->where('rule_id', $ruleId);
                    }
                })
                    //->skip(($request->page - 1) * $request->pageSize)
                    //->take($request->pagesize)
                    ->with('condition_type', 'rule', 'rule.scenario');
                    //->get();

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first(),
                    'rule.scenario'
                );

                $result['data'] = $data->get();
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
