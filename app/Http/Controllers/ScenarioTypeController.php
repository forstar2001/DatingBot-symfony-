<?php

namespace App\Http\Controllers;

use App\Models\ScenarioType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ScenarioService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;

class ScenarioTypeController extends Controller
{

    private $category = 'Scenarios';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function get(Request $request) {

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;

        try {
            if (!isset($request->page) || !isset($request->pagesize)) {
                $data = ScenarioType::get();
            } else {
                $data = ScenarioType::skip(($request->page - 1) * $request->pageSize)
                    ->take($request->pagesize)
                    ->get();
            }
            $result['data'] = $data;
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

}
