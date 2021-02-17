<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Weidner\Goutte\GoutteFacade as Goutte;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard.index');
    }




    public function analyseUrl(Request $request)
    {
        $urlParts = parse_url($request->url);
        $url = $request->url;
        $baseUrl = $urlParts['scheme'].'://'.$urlParts['host'];

        $crawler = Goutte::request('GET', $url);

        $results = [];

        $results['meta'] = get_meta_tags($url);



        $results['title'] = [];

        $crawler->filter('title')->each(function ($node) use (&$results) {
            array_push($results['title'], ['value' => $node->text(), 'length' => strlen($node->text())]);
        });


        
        $results['src_links'] = [];
        
        $crawler->filter('link')->each(function ($node) use (&$results) {
            array_push($results['src_links'], ['rel' => $node->attr('rel'), 'value' => $node->attr('href')]);
        });
        
        
        
        $results['links'] = [];
        
        $crawler->filter('a')->each(function ($node) use (&$results) {
            array_push($results['links'], ['text' => $node->text(), 'value' => $node->attr('href')]);
        });
        
        

        $results['images'] = [];

        $crawler->filter('img')->each(function ($node) use ($baseUrl, &$results) {

            $imgData = [];
            $imgData['src'] = $node->attr('src');
            $imgData['alt'] = $node->attr('alt') ? $node->attr('alt') : null;

            $src = $imgData['src'];

            if (!Str::startsWith($imgData['src'], 'http://') && !Str::startsWith($imgData['src'], 'https://'))
            {
                $src = $baseUrl . $imgData['src'];
            }

            list($width, $height, $type, $attr) = getimagesize($src);

            if ($width && $height)
            {
                $imgData['width'] = $width;
                $imgData['height'] = $height;
            }

            if (false)
            {
                $ch = curl_init($src);
    
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, TRUE);
                curl_setopt($ch, CURLOPT_NOBODY, TRUE);
               
                $data = curl_exec($ch);
                $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
               
                curl_close($ch);
                echo $data;
            }
            
            $imgData['mime'] = image_type_to_mime_type($type);

            array_push($results['images'], $imgData);
        });

        return $results;
    }
}
