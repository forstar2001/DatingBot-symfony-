<?php

namespace App\Http\Controllers;

use App\Models\ConditionType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class ConditionTypeController extends Controller
{

    private $category = 'Conditions';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function get(Request $request) {

        $result['success'] = true;

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            if (!isset($request->page) || !isset($request->pagesize)) {
                $data = ConditionType::get();
            } else {
                $data = ConditionType::skip(($request->page - 1) * $request->pageSize)
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
