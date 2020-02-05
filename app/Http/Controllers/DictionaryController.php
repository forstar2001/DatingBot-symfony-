<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Http\Request;
use App\Services\DictionaryService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class DictionaryController extends Controller
{
    private $category = 'Dictionaries';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $dict = new Dictionary();
        $dict->tablename = $request->tablename;
        $dict->name = $request->name;

        if (!RoleCheckingHelper::checkIfActionAuthorized($dict,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            DictionaryService::createDictionary($dict);
            $result['dictionary_id'] = $dict->id;
            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        }
        catch (PDOException $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
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

            $dict = Dictionary::find($request->id);
            if (empty($dict)) {
                $result['success'] = false;
                $result['errors'] = array("Dictionary with id $request->id not found");
                return json_encode($result);
            }

            if (!RoleCheckingHelper::checkIfActionAuthorized($dict,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $currentTablename = $dict->tablename;
            $dict->tablename = $request->tablename;
            $dict->name = $request->name;

            if ($currentTablename !== $dict->tablename)
                DictionaryService::renameDictionary($dict, $currentTablename);

            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        }
        catch (PDOException $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
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

        $dict = Dictionary::find($request->id);

        if (empty($dict)) {
            $result['success'] = false;
            $result['errors'] = array("Dictionary with id $request->id not found");
            return json_encode($result);
        }
        if (!RoleCheckingHelper::checkIfActionAuthorized($dict,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            //$this->authorize('delete', $scenario);
            DictionaryService::removeDictionaryFromDb($dict);
            $dict->delete();
            return json_encode($result);
        }
        catch (PDOException $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
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
                $dict = Dictionary::with('dictionary_values')
                    ->findOrFail($id);
                $result['dictionary'] = $dict;
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


        if ($result['success'] == true) {
            try {
                $data = Dictionary::skip(($request->page - 1) * $request->pageSize)
                    ->take($request->pagesize)
                    //->with('dictionary_values')
                    ->get();

                $result['data'] = $data;
                $result['dictionary_prefix'] = config('database.dictionary_prefix');
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
