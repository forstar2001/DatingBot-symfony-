<?php

namespace App\Http\Controllers;

use App\Helpers\CheckLinkHelper;
use App\Jobs\CheckLink;
use App\Models\BotProfile;
use App\Models\Source;
use App\Models\SourceBotProfileStatuses;
use App\Models\SourcesBotProfiles;
use App\Models\TwoFactorAuthSecret;
use App\User;
use Illuminate\Http\Request;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
         * @var $$botProfile BotProfile
         */
        $source = SourcesBotProfiles::find(83);

        dispatch(new CheckLink($source));

    }
}
