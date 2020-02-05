<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        /*$user = Auth::user();


        if($user->isAdmin())
            return $next($request);


        if (is_array($roles)) {
            foreach ($roles as $role) {
                // Check if user has the role This check will depend on how your roles are set up
                if ($user->hasRole($role))
                    return $next($request);
            }
        } else {
            if ($user->hasRole($roles))
                return $next($request);
        }


        return (new Response('Action is not authorized', 403)); */

    }
}
