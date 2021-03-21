<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function article(Request $request)
    {
        $article = Article::with(['author', 'category'])->firstWhere('url', $request->article);

        if (!$article)
        {
            return abort(404);
        }

        return view('static.article', [
            'article' => $article,
        ]);
    }

    public function allArticles(Request $request)
    {
        return view('static.resources', [
            'articles' => Article::with(['author', 'category'])->get(),
        ]);
    }

    public function category(Request $request)
    {}

    public function allCategories(Request $request)
    {}

    public function author(Request $request)
    {}

    public function allAuthors(Request $request)
    {}
}
