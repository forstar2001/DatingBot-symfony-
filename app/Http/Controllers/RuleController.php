<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rule;
use App\Services\RuleService;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Scenario;

class RuleController extends Controller
{
    private $category = 'Rules';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        try {
            $scenario = Scenario::findOrFail($request->scenario_id);
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

        $rule = new Rule();
        $rule->order = $request->order;
        $rule->name = $request->name;
        $rule->scenario_id = $request->scenario_id;

        try {
            RuleService::createRule($rule);
            $result['rule_id'] = $rule->id;
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

        try {

            $rule = Rule::find($request->id);
            if (empty($rule)) {
                $result['success'] = false;
                $result['errors'] = array("Rule with id $request->id not found");
                return json_encode($result);
            }

            $scenario = Scenario::findOrFail($rule->scenario_id);

            if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $rule->name = $request->name;
            $rule->order = $request->order;
            $rule->scenario_id = $request->scenario_id;

            RuleService::updateRule($rule);
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

        $rule = Rule::find($request->id);

        if (empty($rule)) {
            $result['success'] = false;
            $result['errors'] = array("Rule with id $request->id not found");
            return json_encode($result);
        }
        try {
            //$this->authorize('delete', $scenario);

            $scenario = Scenario::findOrFail($rule->scenario_id);

            if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionRemove)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $rule->delete();
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
                $rule = Rule::with(['conditions', 'scenario', 'scenario.rules'])
                    ->where('id', $id);

                $rule = RoleCheckingHelper::filterDataByViewRoles($rule,
                    RoleCategorie::where('name', $this->category)->first(),
                    'scenario')
                    ->first();

                $result['rule'] = $rule;
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

        $scenarioId = 0;
        if (isset($request->parent_id)) {
            $scenarioId = $request->parent_id;
        }

        if ($result['success'] == true) {
            try {
                $data = Rule::where(function($q) use ($scenarioId) {
                    if ($scenarioId > 0) {
                        $q->where('scenario_id', $scenarioId);
                    }
                })
                    //->skip(($request->page - 1) * $request->pageSize)
                    //->take($request->pagesize)
                    ->with('conditions');
                    //->get();

                $data = RoleCheckingHelper::filterDataByViewRoles($data,
                    RoleCategorie::where('name', $this->category)->first(),
                    'scenario'
                );
                $result['total_rows'] = $data->count();
                $result['data'] = $data
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->orderBy('order')
                    ->get();
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
