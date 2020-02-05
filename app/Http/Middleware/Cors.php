<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->getMethod() == 'OPTIONS'){

            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods'=>'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding, X-Auth-Token, content-type, authorization'
            ];
            return (new Response(' ', 200))
                ->withHeaders($headers);
        }

        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding, X-Auth-Token, content-type, authorization');
            //->header('Access-Control-Allow-Credentials', 'true');
    }
}
