<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCategoryCollection;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\AuthorProfileResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function getProfileOfUser()
    {
        $author = new AuthorProfileResource(User::find(Auth::id())->author_profile()->first());
        return $author;
    }

    public function getAllArticlesOfUser()
    {
        $author = User::find(Auth::id())->author_profile()->first();

        if (!$author)
        {
            return response('UNAUTHORIZED', 403);
        }

        return new ArticleCollection(Article::where('author_id', $author->id)->get());
    }

    public function getAllArticleCategories()
    {
        $author = User::find(Auth::id())->author_profile()->first();

        if (!$author)
        {
            return response('UNAUTHORIZED', 403);
        }

        return new ArticleCategoryCollection(ArticleCategory::all());
    }

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
        $article = Article::find($request->articleId);

        $article->url = $request->url;
        $article->title = $request->title;
        $article->intro_image = $request->introImage;
        $article->intro_text = $request->introText;
        $article->full_text = strip_tags($request->fullText, '<h1><h2><h3><h4><h5><h6><a><img><br><p><strong><i><b><blockquote><div><li><ul><ol><span>');

        $article->save();

        return $article;
    }

    public function deleteArticle(Request $request)
    {
        $request->validate([
            'articleId' => ['required', 'exists:articles,id'],
        ]);

        Article::find($request->articleId)->delete();

        return $request->articleId;
    }
}
