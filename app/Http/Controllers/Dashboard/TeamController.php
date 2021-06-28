<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\TeamMember;
use App\Models\TeamSite;
use App\Models\User;
use App\Models\UserReportGroup;
use App\Models\UserReportGroupShare;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function getAllTeams(Request $request)
    {
        $teamIds = array_unique(array_merge(TeamMember::where('user_id', Auth::id())->pluck('team_id')->toArray(), Team::where('owner_id', Auth::id())->pluck('id')->toArray()));

        $teams = Team::whereIn('id', $teamIds)->with(['members.user', 'sites', 'subscription' => function($query) {
            $query->select('id','name', 'stripe_status');
        }])->get();

        $teams->each(function($team) {
            $team->is_owner = ($team->owner_id === Auth::id());
        });

        return $teams;
    }



    public function getAllInvites(Request $request)
    {
        return TeamInvite::where('email', Auth::user()->email)->where('status', 'pending')->with('team')->get();
    }



    /**
     * This function creates a team and mutates active_team_id on
     * the user if it's empty.
     * 
     * @param String $name
     * @param String $description
     * @param String $category
     * @return Team
     */
    public function createTeam(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['present', 'max:1000'],
            'category' => ['required', 'string', 'max:100'],
            'plan' => ['required', 'string', 'in:seo_free,seo_low,seo_mid,seo_high'],
            'paymentMethod' => ['required', 'string', 'max:100'],
        ]);

        $subscription = null;

        // Create subscription if plan is selected
        if ($request->plan === 'seo_free')
        {
            if (count(Team::where('owner_id', Auth::id())->where('subscription_id', null)->get()) > config('plans.seo_allowed_free_plans'))
            {
                return response('TOO_MANY_FREE_PLANS', 422);
            }
        }
        else
        {
            $method = null;

            $plan = config('plans.seo_plans.'.$request->plan);

            $method = $request->paymentMethod === 'default' ? $request->user()->defaultPaymentMethod() : $request->user()->findPaymentMethod($request->paymentMethod);

            if (!$method)
            {
                return response('NO_PAYMENT_METHOD', 403);
            }

            $subscription = $request->user()->newSubscription( $request->plan, $plan['stripe_id'] )->quantity(null)->create($method->id);
        }

        $team = Team::create([
            'owner_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'inactive',
            'subscription_id' => $subscription ? $subscription->id : null,
        ]);
        
        // Create owner-member for new team
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $team->owner_id,
            'roles' => ['owner'],
        ]);
        
        $user = User::find(Auth::id());

        // Set users active team id to new team if user doesn't have an active team
        if (!$user->active_team_id)
        {
            $user->active_team_id = $team->id;
            $user->save();
        }

        $team = Team::with(['members.user', 'subscription' => function($query) {
            $query->select('id','name', 'stripe_status');
        }])->find($team->id);
        
        $team->is_owner = true;

        return $team;
    }



    /**
     * This function updates a team.
     * 
     * @param String $id
     * @param String $name
     * @param String $description
     * @param String $category
     * @return Team
     */
    public function updateTeam(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['present', 'max:1000'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $team = $request->team_object;

        $team->name = $request->name;
        $team->description = $request->description;
        $team->category = $request->category;
        $team->save();

        $team = Team::with('members.user')->find($team->id);
        $team->is_owner = true;

        return $team;
    }


    /**
     * This function deletes a team.
     * 
     * @param String $id Team ID
     * @return String Team ID
     */
    public function deleteTeam(Request $request)
    {
        $request->team_object->delete();

        return $request->teamId;
    }



    /**
     * 
     */
    public function createTeamSite(Request $request)
    {
        $request->validate([
            'host' => ['required', 'string', 'max:255'],
        ]);

        $host = parse_url($request->host, PHP_URL_HOST);

        if (!$host)
        {
            $host = $request->host;
        }

        $host = strtolower($host);
        
        if (!preg_match("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/", $host))
        {
            return response('INVALID_HOSTNAME', 422);
        }

        $teamSite = TeamSite::firstOrCreate([
            'team_id' => $request->team_object->id,
            'host' => $host,
        ]);

        return $teamSite;
    }



    /**
     * 
     */
    public function deleteTeamSite(Request $request)
    {
        $request->validate([
            'siteId' => ['required', 'exists:team_sites,id'],
        ]);

        TeamSite::find($request->siteId)->delete();

        return $request->siteId;
    }



    /**
     * This function creates an invite for a specific team.
     * 
     * @param String $inviteName Email or User ID of the user to invite
     * @param String $teamId Team ID
     * @return TeamInvite
     */
    public function createInvite(Request $request)
    {
        $request->validate([
            'inviteName' => ['required', 'string', 'max:1000'],
            'teamId' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::find($request->teamId);

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
     * This function sets the active team id of the user.
     */
    public function setActiveTeamId(Request $request)
    {
        $user = User::find(Auth::id());
        $team = $request->team_object;

        $user->active_team_id = $team->id;
        $user->save();

        return $team->id;
    }



    /**
     * This function handles the team invites i.e. can be executed by the user to
     * accept or ignore an invite. If the user accepts the invite a new team member for
     * the respective team is created and the team + new member is returned.
     * 
     * IMPORTANT: mutates active_team_id if empty
     * 
     * @param String $inviteId Team Invite ID
     * @param String $action accepted or ignored
     * @return Team Returns Team object when user accepts
     * @return String Returns Team Invite ID when user ignores
     */
    public function handleInvite(Request $request)
    {
        $request->validate([
            'inviteId' => ['required', 'exists:team_invites,id'],
            'action' => ['required', 'in:accepted,ignored'],
        ]);

        $invite = TeamInvite::find($request->inviteId);

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
            return $invite->id;
        }
    }



    /**
     * This function lets the user leave a team by deleting its
     * corresponding member entry. (Owners excluded)
     * 
     * @param String $teamId Team ID
     * @return String Team ID
     */
    public function leaveTeam(Request $request)
    {
        $member = TeamMember::where('team_id', $request->team_object->id)->firstWhere('user_id', Auth::id());

        // Stops owner from leaving team
        if ($request->team_object->owner_id === Auth::id())
        {
            return response('OWNER_CANNOT_LEAVE_TEAM', 403);
        }

        $member->delete();
        
        return $request->teamId;
    }



    /**
     * This function deletes a member from a team.
     * 
     * @param String $id Team ID
     * @param String $memberId Member ID
     * @return String Member ID
     */
    public function deleteMember(Request $request)
    {
        $request->validate([
            'memberId' => ['required', 'exists:team_members,id'],
        ]);

        $member = TeamMember::where('team_id', $request->team_object->id)->firstWhere('id', $request->memberId);

        if (!$member)
        {
            return response('MEMBER_NOT_FOUND', 404);
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
