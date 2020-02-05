<?php

namespace App\Http\Controllers;

use App\Models\DictionaryValue;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Http\Request;
use App\Services\DictionaryValueService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class DictionaryValueController extends Controller
{
    private $category = 'Dictionaries';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $value = new DictionaryValue();
        $value->name = $request->name;
        $value->description = $request->description;
        $value->dictionary_id = $request->dictionary_id;
        $value->order = $request->order;
        if (!RoleCheckingHelper::checkIfActionAuthorized($value,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            $id = DictionaryValueService::createDictionaryValue($value);
            $result['dictionary_value_id'] = $id;
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

            $value = new DictionaryValue();
            $value->name = $request->name;
            $value->id = $request->id;
            $value->description = $request->description;
            $value->dictionary_id = $request->dictionary_id;
            $value->order = $request->order;

            if (!RoleCheckingHelper::checkIfActionAuthorized($value,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            DictionaryValueService::updateDictionaryValue($value);

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

        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        try {

            $value = new DictionaryValue();
            $value->dictionary_id = $request->dictionary_id;
            $value->name = $request->name;
            $value->description = $request->description;
            $value->id = $request->id;

            if (!RoleCheckingHelper::checkIfActionAuthorized($value,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionRemove)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            DictionaryValueService::removeDictionaryValue($value);
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getById(Request $request, $id, $dictionaryId) {

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

        if (empty($dictionaryId)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'dictionaryId' is required");
        }

        if ($result['success']) {
            try {
                $value = DictionaryValueService::getDictionaryValue($id, $dictionaryId);
                $value->dictionary_id = $dictionaryId;
                $result['dictionary_value'] = $value;
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

        if (!isset($request->page)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'page' is required");
        }

        if (!isset($request->pagesize)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'pagesize' is required");
        }

        if (!isset($request->parent_id)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'parent_id' is required");
        }


        if ($result['success'] == true) {
            try {
                $data = DictionaryValueService::getDictionaryValues($request->parent_id, $request->page, $request->pagesize);

                $result['data'] = $data;
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
