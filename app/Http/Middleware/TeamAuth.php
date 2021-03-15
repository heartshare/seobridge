<?php

namespace App\Http\Middleware;

use App\Models\Team;
use App\Models\TeamMember;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamAuth
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
    public function handle(Request $request, Closure $next, $role = false)
    {
        // [1] Checks for teamId
        $request->validate([
            'teamId' => ['required', 'exists:teams,id'],
        ]);



        // [2] Inserts team-model into request
        $team = Team::find($request->teamId);
        $request->team_object = $team;



        // [3] Checks for user permission
        $permissionLevels = [
            'owner'  => ['owner'],
            'admin'  => ['owner', 'admin'],
            'member' => ['owner', 'admin', 'member'],
        ];

        if ($role)
        {
            $member = TeamMember::where('team_id', $team->id)->firstWhere('user_id', Auth::id());

            // Checks if team has member entry associated with the user in question
            // Also checks if member entry has any of the roles required for futher access
            if (!isset($member) || !array_intersect($permissionLevels[$role], $member->roles))
            {
                return response('UNAUTHORIZED', 403);
            }


            // Additional check for owner role
            // This is just in case a user is falsely assigned the owner role
            if ($role === 'owner' && $team->owner_id !== Auth::id())
            {
                return response('UNAUTHORIZED', 403);
            }
        }

        return $next($request);
    }
}
