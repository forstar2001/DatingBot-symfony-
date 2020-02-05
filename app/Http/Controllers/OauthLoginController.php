<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use League\Flysystem\Exception;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class OauthLoginController extends Controller
{


    public function token(Request $request)
    {
        //return json_encode( ['error' => json_decode($input)]);
        try {
            if (empty($request->grant_type)) {
                return json_encode(['error' => 'Parameter "grant_type" is missed']);
            }

            if ($request->grant_type == 'password') {

                $data = [
                    'client_id' => config('app.password_client_id'),
                    'client_secret' => config('app.password_client_secret'),
                    'grant_type' => $request->grant_type,
                    'username' => $request->username,
                    'password' => $request->password,
                    'scope' => $request->scope
                ];
            } else if ($request->grant_type == 'refresh_token') {
                $data = [
                    'client_id' => config('app.password_client_id'),
                    'client_secret' => config('app.password_client_secret'),
                    'grant_type' => $request->grant_type,
                    'username' => $request->username,
                    'password' => $request->password,
                    'refresh_token' => $request->refresh_token
                ];
            } else {
                return json_encode("Exception!: Invalid 'grant_type' value" );
            }
            $options = [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ];

            $client = new Client($options);

            $response = $client->post( config('app.url') . '/oauth/token', [
                'form_params' => $data,
                'timeout' => '15'
            ]);

            $data = (object)[];
            $data->code = 200;
            $data->data = json_decode($response->getBody()->getContents());

            $user = \App\User::where('email', $request->username)
                ->with(['roles',
                    'roles.role.role_definitions'])->firstOrFail();

            $data->userInfo = $user;
            return json_encode($data);
        }
        catch (GuzzleException $e) {
            $data = (object)[];
            $data->error_message = $e->getMessage();
            $data->code = $e->getCode();
            return json_encode($data);
        }


    }

    public function isRegistered(Request $request)
    {
        $result['success'] = true;
        if (!isset($request->username) && !isset($request->password)){
            $result['success'] = false;
            $result['errors'] = array('Email and password is required');
            return json_encode($result);
        }
        try{
            if(Auth::attempt(['email'=>$request->username,'password'=>$request->password])){
                $user = User::with('twoFactorAuthSecret')
                    ->where('email', $request->username)->firstOrFail();
                $result['user'] = $user;
                return json_encode($result);
            }

            $result['success'] = false;
            $result['errors'] = array('Invalid email and/or password');
            return json_encode($result);
        }catch (\Exception $e){
            $data = (object)[];
            $data->error_message = $e->getMessage();
            $data->code = $e->getCode();
            return json_encode($data);
        }
    }

    public function checkCode(Request $request)
    {
        $result['success'] = true;
        try{
            $user = User::findOrFail($request->userId);
            $googleAuthenticator = new GoogleAuthenticator();
            $secret = $user->twoFactorAuthSecret->secret;

            if ($googleAuthenticator->checkCode($secret, $request->two_factor_auth)) {
                return json_encode($result);
            }

            $result['success'] = false;
            $result['errors'] = array('Invalid email and/or password');
            return json_encode($result);
        }catch (\Exception $e){
            $data = (object)[];
            $data->error_message = $e->getMessage();
            $data->code = $e->getCode();
            return json_encode($data);
        }
    }
}
