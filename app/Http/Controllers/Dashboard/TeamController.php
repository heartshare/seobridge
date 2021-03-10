<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserReportGroup;
use App\Models\UserReportGroupShare;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function getAllTeams(Request $request)
    {
        $teamIds = array_unique(array_merge(TeamMember::where('user_id', Auth::id())->pluck('team_id')->toArray(), Team::where('owner_id', Auth::id())->pluck('id')->toArray()));

        $teams = Team::whereIn('id', $teamIds)->with('members.user')->get();

        $teams->each(function($team) {
            $team->is_owner = ($team->owner_id === Auth::id());
        });

        return $teams;
    }



    public function getAllInvites(Request $request)
    {
        return TeamInvite::where('email', Auth::user()->email)->where('status', 'pending')->with('team')->get();
    }



    public function updateOrCreateTeam(Request $request)
    {
        $request->validate([
            'id' => ['max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['present', 'max:1000'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $teamId = $request->id ? $request->id : null;

        $team = Team::updateOrCreate([
            'id' => $teamId,
        ], [
            'owner_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
        ]);

        $owner = TeamMember::firstOrCreate([
            'team_id' => $team->id,
            'user_id' => $team->owner_id,
        ], [
            'roles' => ['owner'],
        ]);

        return Team::with('members.user')->find($team->id);
    }


    /**
     * Checks if teams owner is same as authorized user then deletes team
     * @param team_id $id
     */
    public function deleteTeam(Request $request)
    {
        $request->validate([
            'id' => ['exists:teams,id'],
        ]);

        $team = Team::find($request->id);

        if ($team->owner_id !== Auth::id())
        {
            return response('UNAUTHORIZED', 403);
        }

        $team->delete();

        return response($request->id, 200);
    }



    public function createInvite(Request $request)
    {
        $request->validate([
            'inviteName' => ['required', 'string', 'max:1000'],
            'teamId' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::find($request->teamId);

        // Checks if user is authorized to invite users
        if ($team->owner_id !== Auth::id())
        {
            return response('UNAUTHORIZED', 403);
        }

        $email = null;
        $userId = null;

        // Probe for user; looks if inviteName is a valid user ID
        $user = User::firstWhere('id', $request->inviteName);

        if ($user)
        {
            $email = $user->email;
            $userId = $user->id;
        }
        else
        {
            $request->validate([
                'inviteName' => ['email'],
            ]);

            $email = $request->inviteName;
        }

        $invite = TeamInvite::create([
            'team_id' => $team->id,
            'email' => $email,
            'user_id' => $userId,
        ]);

        return $invite;
    }



    public function acceptInvite(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:team_invites,id'],
        ]);

        $invite = TeamInvite::find($request->id);

        $invite->status = 'accepted';

        $invite->save();

        
        $member = TeamMember::create([
            'team_id' => $invite->team_id,
            'user_id' => Auth::id(),
            'roles' => ['member'],
        ]);
        
        return Team::with('members.user')->find($invite->team_id);
    }
}
