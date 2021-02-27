<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function getAllTeams(Request $request)
    {
        $teams = array_merge(TeamMember::where('user_id', Auth::id())->pluck('team_id')->toArray(), Team::where('owner_id', Auth::id())->pluck('id')->toArray());
        $teams = array_unique($teams);

        return Team::whereIn('id', $teams)->with('members')->get();
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
            'owner_id' => Auth::id(),
        ], [
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
        ]);

        return $team;
    }



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
}
