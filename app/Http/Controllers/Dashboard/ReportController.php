<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserReport;
use App\Models\UserReportGroup;
use App\Models\UserReportGroupShare;
use App\Models\UserReportTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function getPaginatedReportGroups(Request $request)
    {
        $request->validate([
            'page' => ['integer'],
            'size' => ['integer', 'max:100', 'min: 5'],
            'searchKey' => ['string','nullable'],
            'order' => ['in:ASC,DESC'],
        ]);

        $userTeamIds = User::find(Auth::id())->teams()->pluck('teams.id')->toArray();

        // Get all report group ids that are related to the user (shared, assigned and own reports)
        $assignedReportGroupIds = UserReportGroupShare::where('owner_id', '!=', Auth::id())->where('assigned_to_user_id', Auth::id())->pluck('report_group_id')->toArray();
        
        // Queries report groups where:
        // INDIRECT SHARE: User doesn't own the report group; user is in team referenced by team_id; user_id === null; assigned_to_user_id === null
        // DIRECT SHARE:   User doesn't own the report group; user_id === Auth::id()
        $sharedReportGroupIds = array_merge([],                    UserReportGroupShare::where('owner_id', '!=', Auth::id())->whereIn('team_id', $userTeamIds)->where('user_id', null)->where('assigned_to_user_id', null)->pluck('report_group_id')->toArray());
        $sharedReportGroupIds = array_merge($sharedReportGroupIds, UserReportGroupShare::where('owner_id', '!=', Auth::id())->where('user_id', Auth::id())->pluck('report_group_id')->toArray());
        
        $ownReportGroupIds = UserReportGroup::where('user_id', Auth::id())->pluck('id')->toArray();

        // Merge all ids and make them unique (so we dont have items with the same id)
        $reportGroupIds = array_unique(array_merge($assignedReportGroupIds, $sharedReportGroupIds, $ownReportGroupIds));

        $reports = UserReportGroup::whereIn('id', $reportGroupIds)
        ->where('host', 'LIKE', "%{$request->searchKey}%")
        ->with('task')
        ->orderBy('created_at', $request->order)
        ->simplePaginate($request->size);

        // Fetches first five reports of report group and assigns values for where the group originated (assigned / shared / own)
        // I don't know how performant this is but I can imagine not very much
        // Edit: On the other hand there are only 100 entries at most (or however high the max size number is)
        // TODO: find a better way to do this
        $reports->each(function($report) use ($assignedReportGroupIds, $sharedReportGroupIds, $ownReportGroupIds) {
            $report->reports = UserReport::where('report_group_id', $report->id)->limit(5)->get();

            $report->has_been_assigned = false;
            $report->has_been_shared = false;
            $report->is_own = false;

            if (in_array($report->id, $assignedReportGroupIds))
            {
                $report->has_been_assigned = true;
            }
            else if (in_array($report->id, $sharedReportGroupIds))
            {
                $report->has_been_shared = true;
            }
            else if (in_array($report->id, $ownReportGroupIds))
            {
                $report->is_own = true;
                $report->debug1 = $ownReportGroupIds;
            }
        });

        return $reports;
    }


    
    public function requestSiteAnalysis(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
            'mode' => ['required', 'in:full,single'],
            'device' => ['required', 'array'],
        ]);

        $reportGroup = UserReportGroup::create([
            'user_id' => Auth::id(),
            'url' => $request->url,
            'host' => parse_url($request->url, PHP_URL_HOST),
            'mode' => $request->mode,
            'device' => $request->device,
        ]);

        $task = UserReportTask::create([
            'report_group_id' => $reportGroup->id,
            'url' => $reportGroup->url,
            'status' => 'request_pending',
        ]);

        $response = Http::post('http://localhost:999/request-site-analysis', [
            'url' => $reportGroup->url,
            'mode' => $reportGroup->mode,
            'device' => $reportGroup->device,
            'job_id' => $reportGroup->id,
        ]);

        $task->status = ($response->status() > 199 && $response->status() < 300) ? 'request_successful' : 'request_denied';
        $task->save();

        // TODO: send back report group with reports and task
        return 'OK';
    }



    public function statusUpdate(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:crawling,crawling_completed,fetching,fetching_completed,completed'],
            'jobId' => ['required', 'exists:user_report_tasks,id'],
        ]);

        $task = UserReportGroup::find($request->jobId)->task();

        if (isset($request->progress) && is_int($request->progress))
        {
            $task->progress = $request->progress;
        }

        if (isset($request->progressMax) && is_int($request->progressMax))
        {
            $task->progress_max = $request->progressMax;
        }

        $task->status = $request->type;
        $task->save();
    }



    public function shareReport(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:group,single'],
            'teamId' => ['required', 'exists:teams,id'],
            'userId' => ['nullable', 'exists:users,id'],
            'assignedToUserId' => ['nullable', 'exists:users,id'],
        ]);

        if ($request->type === 'group')
        {
            $request->validate([
                'id' => ['required', 'exists:user_report_groups,id'],
            ]);

            $share = UserReportGroupShare::create([
                'owner_id' => Auth::id(),
                'report_group_id' => $request->id,
                'team_id' => $request->teamId,
                'user_id' => $request->userId,
            ]);
        }
        else
        {
            return response('INVALID_TYPE', 422);
        }

        if ($request->assignedToUserId)
        {
            // We dont need the user id for an assigned report
            $share->user_id = null;
            
            // Just the 'assign to' user id
            $share->assigned_to_user_id = $request->assignedToUserId;

            $share->is_assigned = true;

            $share->save();
        }

        return $share;
    }



    public function addPage(Request $request)
    {
        $reportJson = $request->data;

        $reportGroup = UserReportGroup::find($request->id);

        UserReport::create([
            'report_group_id' => $reportGroup->id,
            'url' => $reportJson['url']['href'],
            'host' => $reportJson['url']['host'],
            'device' => ['viewport' => [1920, 1080]],
            'score' => $reportJson['score']['totalPageScore'],
            'data' => $reportJson,
        ]);
    }



    public function deleteReport(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:user_report_groups,id'],
        ]);

        $reportGroup = UserReportGroup::find($request->id);
        
        if ($reportGroup->user_id !== Auth::id())
        {
            return response('UNAUTHORIZED', 403);
        }

        $id = $reportGroup->id;

        $reportGroup->delete();

        return $id;
    }
}
