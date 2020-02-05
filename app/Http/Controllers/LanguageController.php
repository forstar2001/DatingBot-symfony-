<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class LanguageController extends Controller
{

    private $category = 'Languages';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';
    private $englishLanguageId = 1;

    public function get(Request $request) {

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;

        try {
            $result['data'] = Language::get();
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
                $language = Language::findOrFail($id);
                $result['language'] = $language;
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

    public function getAsCollection($page = NULL, $pagesize = NULL) {
        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;

        try {

            $data = Language::with('country');
            $countries = Country::where('id', '>', '0');
            $countries = RoleCheckingHelper::filterDataByViewRoles($countries,
                RoleCategorie::where('name', $this->category)->first(),
                NULL, true);

            $data = $data->whereHas('country', function ($q) use ($countries) {
                $q->whereIn('countries.id', $countries->pluck('id')->toArray());
            });

            if (isset($page) && isset($pagesize)) {
                $data = $data
                    ->skip(($page - 1) * $pagesize)
                    ->take($pagesize);
            }
            $data = $data->get();
            if (empty($data->search(function($item, $key) {
                return $item->id == $this->englishLanguageId;
            }))) {
                $data->push(Language::find($this->englishLanguageId));
            }

            return $data;
        }
        catch (\Exception $exception) {
            return collect([$exception->getMessage()]);
        }
    }

}
