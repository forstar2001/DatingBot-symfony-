<?php


namespace App\Http\Controllers;


use App\Models\TwoFactorAuthSecret;
use App\Services\UserManagementService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class TwoFactorAuthenticationController extends Controller
{

    public function create(Request $request)
    {
        $result['success'] = true;

        try {
            $user = User::find($request->userId);
            if (empty($user)) {
                $result['success'] = false;
                $result['errors'] = array("User with id $request->id not found");
                return json_encode($result);
            }
            $currentUser = Auth::user();

            if ($user->id !== $currentUser->id){
                $result['success'] = false;
                $result['errors'] = array('Can not get enabled two factor authentication for another user');
                return json_encode($result);
            }

            $twoFactorAuthSecret = new TwoFactorAuthSecret();
            $twoFactorAuthSecret->status = TwoFactorAuthSecret::enabled;
            $twoFactorAuthSecret->secret = $request->secret;

            $user->twoFactorAuthSecret()->save($twoFactorAuthSecret);

            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function delete(Request $request)
    {
        $result['success'] = true;
        try{
            $user = User::findOrFail($request->id);
            $user->twoFactorAuthSecret()->delete();
            return json_encode($result);

        }catch (\Exception $exception){
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getById(Request $request, $id) {

        $result['success'] = true;

        if (empty($id)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'id' is required");
        }

        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($user->id !== $currentUser->id){
            $result['success'] = false;
            $result['errors'] = array('Can not get enabled two factor authentication for another user');
        }

        if ($result['success']) {
            try {
                $googleAuthenticator = new GoogleAuthenticator();
                $secret = $googleAuthenticator->generateSecret();
                $qrImg = GoogleQrUrl::generate($user->email,$secret, 'Dating%20Bot%20Dashboard');
                $result['code'] = $secret;
                $result['src'] = $qrImg;
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
}