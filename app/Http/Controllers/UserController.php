<?php

namespace App\Http\Controllers;

use App\User;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ScenarioService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{

    public function getById(Request $request, $id)
    {
        $result['success'] = true;
        try{
            $user = User::with('twoFactorAuthSecret')->findOrFail($id);
            $result['user'] = $user;
            return json_encode($result);
        }catch (\Exception $e){
            $data = (object)[];
            $data->error_message = $e->getMessage();
            $data->code = $e->getCode();
            return json_encode($data);
        }
    }

    public function get(Request $request)
    {

        $result['success'] = true;

        $user = Auth::user();

        $name = null;
        $email = null;

        if (isset($request->name)) {
            $name = $request->name;
        }
        if (isset($request->email)) {
            $email = $request->email;
        }

        try {

            if ($user->isAdmin()) {

                if (!isset($request->page, $request->pagesize)) {
                    $data = User::get();
                } else {
                    $data = User::where(function ($q) use ($name, $email) {
                        if ($name !== null) {
                            $names = explode(',', $name);
                            $q->whereIn('id', $names);
                        }
                        if ($email !== null) {
                            $emails = explode(',', $email);
                            $q->whereIn('id', $emails);
                        }
                    })
                        ->skip(($request->page - 1) * $request->pagesize)
                        ->take($request->pagesize)
                        ->get();

                    $result['total_rows'] = User::where(function ($q) use ($name, $email) {
                        if ($name !== null) {
                            $names = explode(',', $name);
                            $q->whereIn('id', $names);
                        }
                        if ($email !== null) {
                            $emails = explode(',', $email);
                            $q->whereIn('id', $emails);
                        }
                    })->count();
                }
            } else {
                //show only current user for non admins
                $data = [$user];
            }
            $result['data'] = $data;
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

    public function update(Request $request)
    {
        $result['success'] = true;

        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        try {

            $user = User::find($request->id);
            if (empty($user)) {
                $result['success'] = false;
                $result['errors'] = array("User with id $request->id not found");
                return json_encode($result);
            }
            $user->name = $request->name;
            $user->email = $request->email;
            if (isset($request->password)) {
                $user->password = Hash::make($request->password);
            }

            UserManagementService::update($user);
            return json_encode($result);
        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function create(Request $request)
    {
        $result['success'] = true;

        if (!isset($request->name, $request->email, $request->password)) {
            $result['success'] = false;
            $result['errors'] = array('All parameters is required');
            return json_encode($result);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        try {
            UserManagementService::create($user);
            $result['user_id'] = $user->id;
            return json_encode($result);
        } catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

    public function delete(Request $request) {

        $result['success'] = true;

        $user = User::find($request->id);

        if (empty($user)) {
            $result['success'] = false;
            $result['errors'] = array("User with id $request->id not found");
            return json_encode($result);
        }
        try {
            $user->roles()->delete();
            $user->delete();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }
}
