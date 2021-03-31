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

    // TODO: NEEDS VALIDATION & AUTH
    public function updateArticle(Request $request)
    {
        $author = User::find(Auth::id())->author_profile()->first();

        if (!$author)
        {
            return response('UNAUTHORIZED', 403);
        }

        $article = Article::find($request->articleId);

        $article->url = $request->url;
        $article->title = $request->title;
        $article->intro_image = $request->introImage;
        $article->intro_text = $request->introText;
        $article->full_text = strip_tags($request->fullText, '<h1><h2><h3><h4><h5><h6><a><img><br><p><strong><i><b><blockquote><div><li><ul><ol><span><del><ins><s><u><em>');
        $article->category_id = $request->categoryId;

        $article->save();

        return $article;
    }

    // TODO: NEEDS VALIDATION & AUTH
    public function setPublishDate(Request $request)
    {
        $author = User::find(Auth::id())->author_profile()->first();

        if (!$author)
        {
            return response('UNAUTHORIZED', 403);
        }

        $date = $request->publishDate === 'immediate' ? now() : $request->publishDate;
        $article = Article::find($request->articleId);

        $article->published_at = $date;

        $article->save();

        return $article;
    }

    // TODO: NEEDS VALIDATION & AUTH
    public function deleteArticle(Request $request)
    {
        $request->validate([
            'articleId' => ['required', 'exists:articles,id'],
        ]);

        Article::find($request->articleId)->delete();

        return $request->articleId;
    }



    // TODO: NEEDS VALIDATION & AUTH
    public function createArticleCategory(Request $request)
    {
        $author = User::find(Auth::id())->author_profile()->first();

        if (!$author)
        {
            return response('UNAUTHORIZED', 403);
        }

        $url = $request->url;

        if (!$request->url)
        {
            $url = strtr($request->name, ['Ä' => 'AE', 'Ö' => 'OE', 'Ü' => 'UE', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', 'ẞ' => 'SS']);
            $url = Str::slug($url, '-');
        }

        $category = ArticleCategory::create([
            'url' => $url,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $category;
    }

    public function updateArticleCategory(Request $request)
    {

    }

    public function deleteArticleCategory(Request $request)
    {
        
    }
}
