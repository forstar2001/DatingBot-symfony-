<?php

namespace App\Http\Controllers;

use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Scenario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\ScenarioService;
use App\Services\RoleCheckingHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ScenarioController extends Controller
{
    private $category = 'Scenarios';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $scenario = new Scenario();
        $scenario->name = $request->name;
        $scenario->description = $request->description;
        $scenario->user_id = Auth::user()->id;
        $scenario->type_id = $request->scenario_type_id;

        if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            ScenarioService::createScenario($scenario);
            $result['scenario_id'] = $scenario->id;
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

            $scenario = Scenario::find($request->id);
            if (empty($scenario)) {
                $result['success'] = false;
                $result['errors'] = array("Scenario with id $request->id not found");
                return json_encode($result);
            }
            $scenario->name = $request->name;
            $scenario->description = $request->description;
            $scenario->type_id = $request->scenario_type_id;

            if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            ScenarioService::updateScenario($scenario);
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

        $scenario = Scenario::find($request->id);

        if (empty($scenario)) {
            $result['success'] = false;
            $result['errors'] = array("Scenario with id $request->id not found");
            return json_encode($result);
        }
        try {
            //$this->authorize('delete', $scenario);

            if (!RoleCheckingHelper::checkIfActionAuthorized($scenario,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionRemove)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $scenario->delete();
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

        if (empty($id)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'id' is required");
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if ($result['success']) {
            try {
                $scenario = Scenario::with(['user', 'scenario_type'])
                    ->where('id',$id);

                $scenario = RoleCheckingHelper::filterDataByViewRoles($scenario,
                    RoleCategorie::where('name', $this->category)->first())->first();

                $result['success'] = true;
                $result['scenario'] = $scenario;
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

        //if (role)

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

        if ($result['success'] == true) {
            //try {


                $data = Scenario::with('user', 'scenario_type', 'rules');
                    //->get();

                $data = RoleCheckingHelper::filterDataByViewRoles($data, RoleCategorie::where('name', $this->category)->first());


                $count = $data->count();
                $data = $data->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->get();

                $result['data'] = $data;
                $result['total_rows'] = $count;
                return json_encode($result);
            //}
            //catch (\Exception $exception) {
            //    $result['success'] = false;
            //    $result['errors'] = array($exception->getMessage());
            //    return json_encode($result);
            //}
        } else {
            return json_encode($result);
        }
    }

}
