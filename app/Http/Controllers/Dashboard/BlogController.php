<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function createArticle(Request $request)
    {
        $url = strtr($request->title, ['Ä' => 'AE', 'Ö' => 'OE', 'Ü' => 'UE', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', 'ẞ' => 'SS']);
        $url = Str::slug($url, '-');

        $article = Article::create([
            'url' => $url,
            'title' => $request->title,
            'author_id' => 'author_root',
        ]);

        return $article;
    }

    public function updateArticle(Request $request)
    {

    }
}
