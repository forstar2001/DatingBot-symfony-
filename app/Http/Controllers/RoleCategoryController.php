<?php

namespace App\Http\Controllers;

use App\Models\RoleCategorie;
use Illuminate\Http\Request;


class RoleCategoryController extends Controller
{

    public function get(Request $request) {


        $result['success'] = true;

        try {
            if (!isset($request->page) || !isset($request->pagesize)) {
                $data = RoleCategorie::orderBy('name')->get();
            } else {
                $data = RoleCategorie::skip(($request->page - 1) * $request->pageSize)
                    ->take($request->pagesize)
                    ->orderBy('name')
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