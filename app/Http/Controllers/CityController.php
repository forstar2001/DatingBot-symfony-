<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Validation\ValidationException;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Country;
use App\Models\Region;


class CityController extends Controller
{
    private $category = 'Cities';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        try {
            $region = Region::findOrFail($request->region_id);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($region,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $item = new City();
        $item->name = $request->name;
        $item->code = $request->code;
        $item->additional_code = $request->additional_code;
        $item->region_id = $request->region_id;

        try {
            CityService::create($item);
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
            $region = Region::findOrFail($request->region_id);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($region,
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

            $item = City::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("City with id $request->id not found");
                return json_encode($result);
            }
            $item->name = $request->name;
            $item->code = $request->code;
            $item->additional_code = $request->additional_code;
            $item->region_id = $request->region_id;

            CityService::update($item);
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

        $item = City::find($request->id);

        try {
            $region = Region::findOrFail($item->region_id);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($region,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("City with id $request->id not found");
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
                $item = City::with(['region', 'region.country'])
                    ->where('id', $id);

                $item = RoleCheckingHelper::filterDataByViewRoles($item,
                    RoleCategorie::where('name', $this->category)->first(),
                    'region')
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

        $countryId = 0;
        if (isset($request->country_id)) {
            $countryId = $request->country_id;
        }



        if ($result['success'] == true) {

            if ($request->page > 0 && $request->pagesize > 0) {

                try {
                    $data = City::where(function ($q) use ($parentId, $countryId) {
                        if ($parentId > 0) {
                            $q->where('region_id', $parentId);
                        }
                        if ($countryId > 0) {
                            $q->where('region.country_id', $countryId);
                        }
                    })
                        //->skip(($request->page - 1) * $request->pagesize)
                        //->take($request->pagesize)
                        ->with(['region', 'region.country'])
                        ->orderBy('name');
                        //->get();

                    $data = RoleCheckingHelper::filterDataByViewRoles($data,
                        RoleCategorie::where('name', $this->category)->first(),
                        'region'
                    );

                    $count = $data->count();

                    $result['data'] = $data->skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize)
                        ->get();
                    $result['total_rows'] = $count;
                    return json_encode($result);
                } catch (\Exception $exception) {
                    $result['success'] = false;
                    $result['errors'] = array($exception->getMessage());
                    return json_encode($result);
                }
            } else {
                try {
                    $data = City::where(function ($q) use ($parentId, $countryId) {
                        if ($parentId > 0) {
                            $q->where('region_id', $parentId);
                        }
                        if ($countryId > 0) {
                            $q->where('region.country_id', $countryId);
                        }
                    })
                        ->with(['region', 'region.country'])
                        ->orderBy('name');
                        //->get();

                    $data = RoleCheckingHelper::filterDataByViewRoles($data,
                        RoleCategorie::where('name', $this->category)->first(),
                        'region'
                    );

                    $count = $data->count();

                    $result['data'] = $data->get();
                    $result['total_rows'] = $count;
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
