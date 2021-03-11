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

        if ($teamId)
        {
            $team = Team::find($teamId);

            // Checks if user is authorized to edit team
            if ($team->owner_id !== Auth::id())
            {
                return response('UNAUTHORIZED', 403);
            }

            $team->name = $request->name;
            $team->description = $request->description;
            $team->category = $request->category;
            $team->save();
        }
        else
        {
            $team = Team::create([
                'owner_id' => Auth::id(),
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'status' => 'inactive',
            ]);
            
            // Create owner-member for new team
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $team->owner_id,
            ], [
                'roles' => ['owner'],
            ]);
            
            $user = User::find(Auth::id());
    
            // Set users active team id to new team if user doesn't have an active team
            if (!$user->active_team_id)
            {
                $user->active_team_id = $team->id;
                $user->save();
            }
        }

        $team = Team::with('members.user')->find($team->id);
        $team->is_owner = true;

        return $team;
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



    /**
     * This function handles the team invites i.e. can be executed by the user to
     * accept or ignore an invite. If the user accepts the invite a new team member for
     * the respective team is created and the team + new member is returned.
     * 
     * @param String $id Team Invite ID
     * @param String $action accepted or ignored
     * @return Team Returns Team object when user accepts
     * @return String Returns string 'OK' when user ignores
     */
    public function handleInvite(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:team_invites,id'],
            'action' => ['required', 'in:accepted,ignored'],
        ]);

        $invite = TeamInvite::find($request->id);

        $invite->status = $request->action;
        $invite->save();

        // If user accepted the invite, create member and return team
        if ($invite->status === 'accepted')
        {
            TeamMember::create([
                'team_id' => $invite->team_id,
                'user_id' => Auth::id(),
                'roles' => ['member'],
            ]);
    
            $user = User::find(Auth::id());
    
            // Set active team id to new team if user doesn't have an active team
            if (!$user->active_team_id)
            {
                $user->active_team_id = $invite->team_id;
                $user->save();
            }
    
            $team = Team::with('members.user')->find($invite->team_id);
            $team->is_owner = ($team->owner_id === Auth::id());
            
            return $team;
        }
        else
        {
            return 'OK';
        }
    }



    /**
     * This function lets the user leave a team by deleting its
     * corresponding member entry. If the user is also the owner
     * (either by having the owner role or being referenced by the teams owner_id)
     * the team gets deleted entirely.
     * 
     * @param String $id Team ID
     * @return String Team ID
     */
    public function leaveTeam(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::find($request->id);
        $member = TeamMember::where('team_id', $team->id)->firstWhere('user', Auth::id());
        $user = User::find(Auth::id());

        // Deletes team if owner leaves
        if (in_array('owner', $member->roles) || $team->owner_id === Auth::id())
        {
            // By deleting the team, all members will be removed as well
            $team->delete();
        }
        else
        {
            // Only deletes member
            $member->delete();
        }
        
        // Reset active team id on user if user leaves active team
        if ($user->active_team_id == $request->id)
        {
            $user->active_team_id = null;
            $user->save();
        }
        
        return $request->id;
    }



    public function deleteMember(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:teams,id'],
            'memberId' => ['required', 'exists:team_members,id'],
        ]);

        $team = Team::find($request->id);
        $member = TeamMember::where('team_id', $team->id)->firstWhere('id', $request->memberId);

        if (!$member)
        {
            return response('MEMBER_NOT_FOUND', 404);
        }
        
        // Checks if user is authorized to delete member
        if ($team->owner_id !== Auth::id())
        {
            return response('UNAUTHORIZED', 403);
        }

        // Prevent user from deleting self
        if ($member->user_id === Auth::id())
        {
            return response('CANNOT_DELETE_SELF', 403);
        }
        
        // Just in case: prevent user from deleting owner
        if (in_array('owner', $member->roles))
        {
            return response('CANNOT_DELETE_OWNER', 403);
        }

        $member->delete();

        return $request->memberId;
    }
}
