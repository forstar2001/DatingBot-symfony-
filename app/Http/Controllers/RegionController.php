<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Services\RegionService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class RegionController extends Controller
{
    private $category = 'Regions';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $item = new Region();
        $item->name = $request->name;
        $item->code = $request->code;
        $item->additional_code = $request->additional_code;
        $item->country_id = $request->country_id;

        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            RegionService::create($item);
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

            $item = Region::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("Region with id $request->id not found");
                return json_encode($result);
            }

            if (!RoleCheckingHelper::checkIfActionAuthorized($item,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $item->name = $request->name;
            $item->code = $request->code;
            $item->additional_code = $request->additional_code;
            $item->country_id = $request->country_id;

            RegionService::update($item);
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

        $item = Region::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Region with id $request->id not found");
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
                $item = Region::with(['country', 'cities'])
                    ->where('id', $id);

                $item = RoleCheckingHelper::filterDataByViewRoles($item,
                    RoleCategorie::where('name', $this->category)->first()
                )->first();

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


        $parentId = 0;
        if (isset($request->parent_id)) {
            $parentId = $request->parent_id;
        }

        if ($result['success'] == true) {

            if ($request->page > 0 && $request->pagesize > 0) {

                try {
                    $data = Region::where(function ($q) use ($parentId) {
                        if ($parentId > 0) {
                            $q->where('country_id', $parentId);
                        }
                    })
                        //->skip(($request->page - 1) * $request->pagesize)
                        //->take($request->pagesize)
                        ->with(['country', 'cities'])
                        ->orderBy('name');
                        //->get();

                    $data = RoleCheckingHelper::filterDataByViewRoles($data,
                        RoleCategorie::where('name', $this->category)->first()
                    );

                    $result['data'] = $data
                        ->skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize)
                        ->get();
                    $result['total_rows'] = $data->count();
                    return json_encode($result);
                } catch (\Exception $exception) {
                    $result['success'] = false;
                    $result['errors'] = array($exception->getMessage());
                    return json_encode($result);
                }
            } else {
                try {
                    $data = Region::where(function ($q) use ($parentId) {
                        if ($parentId > 0) {
                            $q->where('country_id', $parentId);
                        }
                    })
                        ->with(['country', 'cities']);

                    $data = RoleCheckingHelper::filterDataByViewRoles($data,
                        RoleCategorie::where('name', $this->category)->first()
                    );

                    $data = $data->orderBy('name')->get();

                    $result['data'] = $data;
                    $result['total_rows'] = Region::where(function ($q) use ($parentId) {
                        if ($parentId > 0) {
                            $q->where('country_id', $parentId);
                        }
                    })->count();
                    return json_encode($result);
                } catch (\Exception $exception) {
                    $result['success'] = false;
                    $result['errors'] = array($exception->getMessage());
                    return json_encode($result);
                }
            }
        } else {
            return json_encode($result);
        }
    }

}
