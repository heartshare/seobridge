<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserReport;
use App\Models\UserReportTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function getAllReports(Request $request)
    {
        return UserReport::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();
    }


    
    public function requestSiteAnalysis(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
            'mode' => ['required', 'in:full,single'],
            'device' => ['required', 'array'],
        ]);

        $task = UserReportTask::create([
            'user_id' => Auth::id(),
            'url' => $request->url,
            'status' => 'request_pending',
        ]);

        $response = Http::post('http://localhost:999/request-site-analysis', [
            'url' => $task->url,
            'mode' => $request->mode,
            'device' => $request->device,
            'job_id' => $task->id,
        ]);

        $task->status = ($response->status() > 199 && $response->status() < 300) ? 'request_successful' : 'request_denied';
        $task->save();

        return $task;
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:crawling,crawling_completed,fetching,fetching_completed,completed'],
            'jobId' => ['required', 'exists:user_report_tasks,id'],
        ]);

        $task = UserReportTask::find($request->jobId);

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

        $task = UserReportTask::find($request->id);

        $report = UserReport::create([
            'job_id' => $task->id,
            'user_id' => $task->user_id,
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
            'id' => ['required', 'exists:user_reports,id'],
        ]);

        $report = UserReport::find($request->id);
        
        if ($report->user_id !== Auth::id())
        {
            return response('UNAUTHORIZED', 403);
        }

        $id = $report->id;

        $report->delete();

        return $id;
    }
}
