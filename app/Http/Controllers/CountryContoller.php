<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class CountryController extends Controller
{
    private $category = 'Countries';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $item = new Country();
        $item->name = $request->name;
        $item->code = $request->code;
        $item->additional_code = $request->additional_code;
        $item->language_id = $request->language_id == 0 ? null : $request->language_id;

        if (!RoleCheckingHelper::checkIfActionAuthorized($item,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            CountryService::create($item);
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

            $item = Country::find($request->id);
            if (empty($item)) {
                $result['success'] = false;
                $result['errors'] = array("Country with id $request->id not found");
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
            $item->language_id = $request->language_id == 0 ? null : $request->language_id;

            CountryService::update($item);
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

        $item = Country::find($request->id);

        if (empty($item)) {
            $result['success'] = false;
            $result['errors'] = array("Country with id $request->id not found");
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
                $item = Country::with(['regions', 'regions.cities', 'country'])
                    ->findOrFail($id);

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

        if ($result['success'] == true) {
            if ($request->page > 0 && $request->pagesize > 0) {
                try {
                    $data = Country::skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize)
                        ->with(['regions', 'regions.cities', 'language']);

                    $data = RoleCheckingHelper::filterDataByViewRoles($data,
                        RoleCategorie::where('name', $this->category)->first(),null,true);

                    $data = $data->get();

                    $result['data'] = $data;
                    $result['total_rows'] = Country::count();
                    return json_encode($result);
                } catch (\Exception $exception) {
                    $result['success'] = false;
                    $result['errors'] = array($exception->getMessage());
                    $result['line'] = $exception->getLine();
                    return json_encode($result);
                }
            } else {
                try {
                    $data = Country::with(['regions', 'regions.cities', 'language']);

                    $data = RoleCheckingHelper::filterDataByViewRoles($data,
                        RoleCategorie::where('name', $this->category)->first(), null, true);

                    $data = $data->orderBy('name')->get();

                    $result['data'] = $data;
                    $result['total_rows'] = Country::count();

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
