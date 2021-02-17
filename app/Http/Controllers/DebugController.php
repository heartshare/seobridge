<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Weidner\Goutte\GoutteFacade as Goutte;
use Illuminate\Support\Str;

class DebugController extends Controller
{
    public function test()
    {
        $url = 'https://fireship.io/courses/flutter-firebase/';
        $url = 'https://freuwort.com';
        $baseUrl = $url;
        $crawler = Goutte::request('GET', $url);

        $tags = get_meta_tags($url);

        print '<p><fieldset> <legend>Meta Tags</legend>';

        foreach ($tags as $tag => $content)
        {
            switch ($tag) {
                case 'theme-color':
                    print '<p>'. $tag.': <b style="background: '.$content.'; color: white;"> '.$content.' </b></p>';
                    break;
                
                default:
                    print '<p>'. $tag.': <b>'.$content.'</b></p>';
                    break;
            }
        }

        print '</fieldset></p>';

        print '<p><fieldset> <legend>Article</legend>';

        // $crawler->filter('article')->each(function ($node) {
        //     print $node->html()."<br>";
        // });

        print '</fieldset></p>';

        print '<p><fieldset> <legend>Page Title</legend>';

        $crawler->filter('title')->each(function ($node) {
            print $node->text() . ' ('.strlen($node->text()).')<br>';
        });

        $crawler->filter('title')->each(function ($node) {
            print $node->text() . ' ('.strlen($node->text()).')';
        });

        print '</fieldset></p>';

        print '<p><fieldset> <legend>SRC-Links</legend>';

        $crawler->filter('link')->each(function ($node) {
            print $node->attr('rel').': <b>'.$node->attr('href')."</b><br>";
        });

        print '</fieldset></p>';

        print '<p><fieldset> <legend>Links</legend>';

        $crawler->filter('a')->each(function ($node) {
            print $node->attr('href').': <b>'.$node->text()."</b><br>";
        });

        print '</fieldset></p>';

        print '<p><fieldset> <legend>Images</legend>';

        $crawler->filter('img')->each(function ($node) use ($baseUrl) {
            $src = $node->attr('src');
            $alt = $node->attr('alt') ? $node->attr('alt') : '<i>MISSING</i>';

            if (!Str::startsWith($src, 'http://') && !Str::startsWith($src, 'https://'))
            {
                $src = $baseUrl . $src;
            }

            list($width, $height, $type, $attr) = getimagesize($src);

            print '<p><fieldset> <legend>'.$src.'</legend>';

            if ($width && $height)
            {
                print '<p>Size: <b>'.$width . 'px / ' . $height . 'px</b></p>';
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
            

            print '<p>ALT-Tag: <b>' . $alt . '</b></p>';
            print '<p>MIME: <b>' . image_type_to_mime_type($type) . '</b></p>';
            
            print '<img width="300" src="'.$src.'">'."<br>";
            print '</fieldset></p>';
        });

        print '</fieldset></p>';
    }
}
