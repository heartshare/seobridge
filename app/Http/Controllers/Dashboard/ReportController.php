<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserReport;
use App\Models\UserReportGroup;
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
            'size' => ['integer'],
            'searchKey' => ['string','nullable'],
            'order' => ['in:ASC,DESC'],
        ]);

        $reports = UserReportGroup::where('user_id', Auth::id())
        ->where('host', 'LIKE', "%{$request->searchKey}%")
        ->with('task')
        ->orderBy('created_at', $request->order)
        ->simplePaginate($request->size);

        // I don't know how performant this is but I can imagine not very much
        // TODO: find a better way to do this
        $reports->each(function($report) {
            $report->reports = UserReport::where('report_group_id', $report->id)->limit(5)->get();
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
