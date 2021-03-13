<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->teamId)
        {
            return response('MISSING_TEAM_ID', 422);
        }

        $team = Team::find($request->teamId);

        if (!$team)
        {
            return response('UNKNOWN_TEAM_ID', 404);
        }

        if ($role)
        {
            if ($role === 'owner')
            {
                if ($team->owner_id !== Auth::id())
                {
                    return response('UNAUTHORIZED', 403);
                }
            }
        }

        return $next($request);
    }
}
