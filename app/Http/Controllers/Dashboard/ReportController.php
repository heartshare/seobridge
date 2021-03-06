<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\UserReportTaskUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamSite;
use App\Models\User;
use App\Models\UserReport;
use App\Models\UserReportGroup;
use App\Models\UserReportGroupShare;
use App\Models\UserReportTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

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
        $assignedReportGroupIds = UserReportGroupShare::where('owner_id', '!=', Auth::id())->whereIn('team_id', $userTeamIds)->where('assigned_to_user_id', Auth::id())->pluck('report_group_id')->toArray();
        
        // Queries shared report groups where:
        // • user doesn't own the report group
        // • user is in team referenced by team_id
        // • user_id is null or Auth::id
        // • assigned_to_user_id is null
        $sharedReportGroupIds = UserReportGroupShare::where('owner_id', '!=', Auth::id())->whereIn('team_id', $userTeamIds)->whereIn('user_id', [null, Auth::id()])->where('assigned_to_user_id', null)->pluck('report_group_id')->toArray();
        
        // Queries all own reports
        $ownReportGroupIds = UserReportGroup::where('owner_id', Auth::id())->pluck('id')->toArray();

        // Merge all ids and make them unique (so we dont have items with identical ids)
        $reportGroupIds = array_unique(array_merge($assignedReportGroupIds, $sharedReportGroupIds, $ownReportGroupIds));

        $reports = UserReportGroup::whereIn('id', $reportGroupIds)
        ->where('host', 'LIKE', "%{$request->searchKey}%")
        ->with('task')
        ->withCount('reports')
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
            }
        });

        return $reports;
    }



    public function getAllReportsFromGroup(Request $request)
    {
        $request->validate([
            'reportGroupId' => ['exists:user_report_groups,id'],
        ]);

        // TODO: check if report_group_id is accassible for user

        return UserReport::where('report_group_id', $request->reportGroupId)->get();
    }


    
    public function requestSiteAnalysis(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
            'mode' => ['required', 'in:full,single'],
            'device' => ['required', 'array'],
        ]);

        $host = strtolower(parse_url($request->url, PHP_URL_HOST));

        // Check if selected plan requires namespace check
        if($request->team_object->plan['check_namespaces'])
        {
            $siteNamespaces = TeamSite::where('team_id', $request->teamId)->pluck('host')->toArray();
    
            if (!in_array($host, $siteNamespaces))
            {
                return response('UNKNOWN_SITE_NAMESPACE', 404);
            }
        }


        $reportGroup = UserReportGroup::create([
            'owner_id' => Auth::id(),
            'team_id' => $request->teamId,
            'url' => $request->url,
            'host' => $host,
            'mode' => $request->mode,
            'device' => $request->device,
        ]);

        $task = UserReportTask::create([
            'report_group_id' => $reportGroup->id,
            'url' => $reportGroup->url,
            'status' => 'request_pending',
        ]);

        // return config('puppeteer.endpoint');
        $endpoint = config('puppeteer.endpoint').'/request-site-analysis';

        // return $endpoint;
        $response = Http::post($endpoint, [
            'url' => $reportGroup->url,
            'mode' => $reportGroup->mode,
            'device' => $reportGroup->device,
            'job_id' => $reportGroup->id,
            'owner_id' => Auth::id(),
        ]);

        $task->status = ($response->status() > 199 && $response->status() < 300) ? 'request_successful' : 'request_denied';
        $task->save();

        $returnReportGroup = UserReportGroup::with(['reports','task'])->find($reportGroup->id);
        $returnReportGroup->has_been_assigned = false;
        $returnReportGroup->has_been_shared = false;
        $returnReportGroup->is_own = true;

        return $returnReportGroup;
    }



    public function statusUpdate(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:crawling,crawling_completed,fetching,fetching_completed,completed'],
            'jobId' => ['required', 'exists:user_report_tasks,report_group_id'],
            'ownerId' => ['required', 'exists:user_report_groups,owner_id'],
        ]);

        $task = UserReportTask::firstWhere('report_group_id', $request->jobId);

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

        UserReportTaskUpdated::dispatch($request->ownerId, $request->jobId, $request->type, [
            'progress' => $request->progress,
            'progress_max' => $request->progressMax,
        ]);
        
        return $request->ownerId;
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
        $request->validate([
            'jobId' => ['required', 'exists:user_report_groups,id'],
            'ownerId' => ['required', 'exists:user_report_groups,owner_id'],
        ]);

        $reportJson = $request->data;

        $reportGroup = UserReportGroup::find($request->jobId);

        $report = UserReport::create([
            'report_group_id' => $reportGroup->id,
            'url' => $reportJson['url']['href'],
            'host' => $reportJson['url']['host'],
            'device' => ['viewport' => [1920, 1080]],
            'score' => $reportJson['score']['totalPageScore'],
            'data' => $reportJson,
        ]);

        UserReportTaskUpdated::dispatch($request->ownerId, $request->jobId, 'page_added', $report);
    }



    public function deleteReport(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:user_report_groups,id'],
        ]);

        $reportGroup = UserReportGroup::find($request->id);
        
        if ($reportGroup->owner_id !== Auth::id())
        {
            return response('UNAUTHORIZED', 403);
        }

        $id = $reportGroup->id;

        $reportGroup->delete();

        return $id;
    }



    public function requestGuestSiteAnalysis(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
        ]);

        $host = strtolower(parse_url($request->url, PHP_URL_HOST));

        $userId = 'guest_'.Str::uuid();

        $reportGroup = UserReportGroup::create([
            'owner_id' => $userId,
            'team_id' => null,
            'url' => $request->url,
            'host' => $host,
            'mode' => 'single',
            'device' => [],
        ]);

        $task = UserReportTask::create([
            'report_group_id' => $reportGroup->id,
            'url' => $reportGroup->url,
            'status' => 'request_pending',
        ]);

        // return config('puppeteer.endpoint');
        $endpoint = config('puppeteer.endpoint').'/request-site-analysis';

        // return $endpoint;
        $response = Http::post($endpoint, [
            'url' => $reportGroup->url,
            'mode' => $reportGroup->mode,
            'device' => $reportGroup->device,
            'job_id' => $reportGroup->id,
            'owner_id' => $userId,
        ]);

        $task->status = ($response->status() > 199 && $response->status() < 300) ? 'request_successful' : 'request_denied';
        $task->save();

        $cookie = Cookie::forever('_sbgs', $userId); // sbgs: Seo Bridge Guest Session
        return redirect('/report/'.$reportGroup->id)->cookie($cookie);
    }



    public function showGuestReport(Request $request)
    {
        $reportGroup = UserReportGroup::with(['task', 'reports'])->find($request->reportGroupId);

        return $reportGroup;
    }



    public function getMetaData(Request $request)
    {
        function getMetaTags($str)
        {
            $data = array();

            $doc = new \DOMDocument();
            @$doc->loadHTML($str);

            $title = $doc->getElementsByTagName('title')->item(0)->nodeValue;

            $data['title'] = $title;

            foreach ($doc->getElementsByTagName('meta') as $meta)
            {
                $name = $meta->getAttribute('name');
                $property = $meta->getAttribute('property');
                $httpequiv = $meta->getAttribute('http-equiv');

                $content = $meta->getAttribute('content');


                $key = '';
                if ($name) $key = $name;
                else if ($property) $key = $property;
                else if ($httpequiv) $key = $httpequiv;

                $data[$key] = $content;
            }

            // $pattern = '
            // ~<\s*meta\s

            // # using lookahead to capture type to $1
            //     (?=[^>]*?
            //     \b(?:name|property|http-equiv)\s*=\s*
            //     (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
            //     ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
            // )

            // # capture content to $2
            // [^>]*?\bcontent\s*=\s*
            //     (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
            //     ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
            // [^>]*>

            // ~ix';
            
            // if (preg_match_all($pattern, $str, $out))
            // {
            //     return array_combine($out[1], $out[2]);
            // }

            return $data;
        }

        $request->validate([
            'urls' => ['required', 'array'],
        ]);

        $result = [];
        $urls = array_unique($request->urls);
        $urls = array_slice($urls, 0, 30);

        foreach ($urls as $url)
        {
            try
            {
                $result[$url] = getMetaTags(file_get_contents($url));
            }
            catch (\Throwable $th) {}
        }

        return $result;
    }
}
