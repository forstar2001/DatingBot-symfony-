<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\Role;
use App\Services\UserRoleService;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;

class UserRoleController extends Controller
{
    private $category = 'Assigning Roles to User';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $item = new UserRole();
        $item->user_id = $request->user_id;
        $item->role_id = $request->role_id;

        $role = Role::find($item->role_id);

        if (!empty($role)) {
            $roleDefinitions = $role->role_definitions();
        }

        foreach ($roleDefinitions as $definition) {
            if (!RoleCheckingHelper::checkIfActionAuthorized($definition,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionCreate)->first())) {
                return (new Response('Action is not authorized', 403));
            }
        }


        try {
            UserRoleService::create($item);
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

            $item = UserRole::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("User's role with id $request->id not found");
                return json_encode($result);
            }

            $role = Role::find($item->role_id);

            if (!empty($role)) {
                $roleDefinitions = $role->role_definitions();
            }

            $item->user_id = $request->user_id;
            $item->role_id = $request->role_id;

            foreach ($roleDefinitions as $definition) {
                if (!RoleCheckingHelper::checkIfActionAuthorized($definition,
                    RoleCategorie::where('name', $this->category)->first(),
                    RoleAction::where('name', $this->actionUpdate)->first())) {
                    return (new Response('Action is not authorized', 403));
                }
            }

            UserRoleService::update($item);
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

        $item = UserRole::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Role definition with id $request->id not found");
            return json_encode($result);
        }
        try {
            $role = Role::find($item->role_id);

            if (!empty($role)) {
                $roleDefinitions = $role->role_definitions();
            }

            foreach ($roleDefinitions as $definition) {
                if (!RoleCheckingHelper::checkIfActionAuthorized($definition,
                    RoleCategorie::where('name', $this->category)->first(),
                    RoleAction::where('name', $this->actionRemove)->first())) {
                    return (new Response('Action is not authorized', 403));
                }
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
                $item = UserRole::find($id);

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


        $userId = 0;
        if ($request->parent_id > 0) {
            $userId = $request->parent_id;
        }

        if ($result['success'] == true) {
            try {
                $data = UserRole::where(function($q) use ($userId) {
                    if ($userId > 0) {
                        $q->where('user_id', $userId);
                    }
                });

                if ($request->page > 0 && $request->pagesize > 0) {
                    $data = $data
                        ->skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize);
                }

                $data = $data->with('role')->get();

                $result['data'] = $data;
                $result['total_rows'] = UserRole::where(function($q) use ($userId) {
                    if ($userId > 0) {
                        $q->where('user_id', $userId);
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
