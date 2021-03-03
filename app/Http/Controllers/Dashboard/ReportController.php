<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserReport;
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

        $jobId = 'job_'.Str::uuid();

        $response = Http::post('http://localhost:999/request-site-analysis', [
            'url' => $request->url,
            'mode' => $request->mode,
            'device' => $request->device,
            'job_id' => $jobId,
        ]);

        $reportJson = $response->json();

        return $response->status();
        // return $reportJson;
        // $reportJson['preview'] = null;

        // $report = UserReport::create([
        //     'job_id' => 'job_'.Str::uuid(),
        //     'user_id' => Auth::id(),
        //     'url' => $reportJson['url']['href'],
        //     'host' => $reportJson['url']['host'],
        //     'device' => ['viewport' => [1920, 1080]],
        //     'score' => $reportJson['score']['totalPageScore'],
        //     'data' => $reportJson,
        // ]);

        // return $report;
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
