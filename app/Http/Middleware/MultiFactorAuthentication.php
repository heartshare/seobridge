<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultiFactorAuthentication
{
    /**
     * Handle an incoming request.
     * This middleware does three things:
     * 1) Checks if the request has a Team ID
     * 2) Inserts a queried model of the team referenced by the Team ID into the request
     * 3) Checks if the user is authorized to access the route (check via roles)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->is_oauth_user === false && $user->is_mfa_enabled === true && count($user->mfa_methods) >= 1 && session('mfa') !== true)
        {
            session(['returnURL' => $request->url()]);

            return redirect('/mfa');
        }

        return $next($request);
    }
}
