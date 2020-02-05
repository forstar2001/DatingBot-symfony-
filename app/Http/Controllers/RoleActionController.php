<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleAction;
use Illuminate\Http\Request;


class RoleActionController extends Controller
{

    public function get(Request $request) {


        $result['success'] = true;

        try {
            if (!isset($request->page) || !isset($request->pagesize)) {
                $data = RoleAction::get();
            } else {
                $data = RoleAction::skip(($request->page - 1) * $request->pageSize)
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