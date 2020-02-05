<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoleDefinition;
use App\Services\RoleDefinitionService;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;

class RoleDefinitionController extends Controller
{
    private $category = 'Roles';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $item = new RoleDefinition();
        $item->role_id = $request->role_id;
        $item->action_id = $request->action_id;
        $item->category_id = $request->category_id;
        $item->country_id = $request->country_id == 0 ? null : $request->country_id;
        $item->profile_status_type_id = $request->profile_status_type_id == 0 ? null : $request->profile_status_type_id;


        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            RoleDefinitionService::create($item);
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

        try {

            $item = RoleDefinition::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("Role definition with id $request->id not found");
                return json_encode($result);
            }

            $item->role_id = $request->role_id;
            $item->action_id = $request->action_id;
            $item->category_id = $request->category_id;
            $item->country_id = $request->country_id == 0 ? null : $request->country_id;
            $item->profile_status_type_id = $request->profile_status_type_id == 0 ? null : $request->profile_status_type_id;

            if (!RoleCheckingHelper::checkIfActionAuthorized($item,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            RoleDefinitionService::update($item);
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

        $item = RoleDefinition::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Role definition with id $request->id not found");
            return json_encode($result);
        }
        try {
            if (!RoleCheckingHelper::checkIfActionAuthorized($item,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionRemove)->first())) {
                return (new Response('Action is not authorized', 403));
            }

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
                $item = RoleDefinition::find($id);

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


        $roleId = 0;
        if (isset($request->parent_id)) {
            $roleId = $request->parent_id;
        }

        if ($result['success'] == true) {
            try {
                $data = RoleDefinition::where(function($q) use ($roleId) {
                    if ($roleId > 0) {
                        $q->where('role_id', $roleId);
                    }
                })
                    ->select('*', 'role_definitions.id')
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->with(['action',
                        'category',
                        'country',
                        'profile_status_type'])
                    ->join('role_categories', 'role_definitions.category_id', '=', 'role_categories.id')
                    ->orderBy('role_categories.name')
                    ->get();

                $result['data'] = $data;
                $result['total_rows'] = RoleDefinition::where(function($q) use ($roleId) {
                    if ($roleId > 0) {
                        $q->where('role_id', $roleId);
                    }
                })->count();
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
