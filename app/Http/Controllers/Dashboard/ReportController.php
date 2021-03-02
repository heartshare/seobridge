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
    
    public function analyseUrl(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
        ]);

        $response = Http::post('http://localhost:999/analyse', [
            'url' => $request->url,
        ]);

        $reportJson = $response->json();
        $reportJson['preview'] = null;

        $report = UserReport::create([
            'job_id' => 'job_'.Str::uuid(),
            'user_id' => Auth::id(),
            'url' => $reportJson['url']['href'],
            'host' => $reportJson['url']['host'],
            'device' => ['viewport' => [1920, 1080]],
            'score' => $reportJson['score']['totalPageScore'],
            'data' => $reportJson,
        ]);

        return $report;
    }
}
