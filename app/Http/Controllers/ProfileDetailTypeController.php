<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileDetailType;
use App\Services\ProfileDetailTypeService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class ProfileDetailTypeController extends Controller
{
    private $category = 'Profile detail types';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $item = new ProfileDetailType();
        $item->name = $request->name;
        $item->variable_name = $request->variable_name;
        $item->min_value = $request->min_length;
        $item->max_value = $request->max_length;
        //I have no idea why min_value/max_value cast to int correctly without hacks but max string length doesn't
        //work this way (despite it should work)
        $item->max_string_length = (int)$request->max_string_length == 0 ? NULL : $request->max_string_length;
        $item->profile_detail_value_type_id = $request->profile_detail_value_type_id;
        $item->dictionary_id = (int)$request->dictionary_id == 0 ? NULL : $request->dictionary_id;
        $item->regexp = $request->regexp;
        $item->order = $request->order;

        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }


        try {
            ProfileDetailTypeService::createProfileDetailType($item);
            $result['profile_detail_type_id'] = $item->id;
            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['1'] = $item->dictionary_id;
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

            $item = ProfileDetailType::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("Profile detail type with id $request->id not found");
                return json_encode($result);
            }

            if (!RoleCheckingHelper::checkIfActionAuthorized($item,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $item->name = $request->name;
            $item->variable_name = $request->variable_name;
            $item->min_value = $request->min_length;
            $item->max_value = $request->max_length;
            //I have no idea why min_value/max_value cast to int correctly without hacks but max string length doesn't
            //work this way (despite it should work)
            $item->max_string_length = (int)$request->max_string_length == 0 ? NULL : $request->max_string_length;
            $item->profile_detail_value_type_id = $request->profile_detail_value_type_id;
            $item->dictionary_id = (int)$request->dictionary_id == 0 ? NULL : $request->dictionary_id;
            $item->regexp = $request->regexp;
            $item->order = $request->order;

            ProfileDetailTypeService::updateProfileDetailType($item);
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

        $item = ProfileDetailType::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Profile detail type with id $request->id not found");
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            //$this->authorize('delete', $scenario);

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
                $item = ProfileDetailType::with(['dictionary', 'profile_detail_value_type'])
                    ->findOrFail($id);
                $result['profile_detail_type'] = $item;
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

        if ($result['success'] == true) {
            try {
                $data = ProfileDetailType::orderBy('order', 'asc')
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->with(['dictionary', 'profile_detail_value_type'])
                    ->get();

                $result['data'] = $data;

                $result['total_rows'] = ProfileDetailType::count();
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
