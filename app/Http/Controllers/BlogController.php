<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\AuthorProfile;
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

        return view('static.blog.article', [
            'article' => $article,
        ]);
    }

    public function allArticles(Request $request)
    {
        return view('static.blog.articles', [
            'articles' => Article::with(['author', 'category'])->get(),
        ]);
    }

    public function category(Request $request)
    {
        $category = ArticleCategory::firstWhere('url', $request->category);
        
        if (!$category)
        {
            return abort(404);
        }
        
        $articles = Article::with(['author', 'category'])->where('category_id', $category->id)->get();

        return view('static.blog.category-articles', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }

    public function allCategories(Request $request)
    {
        return view('static.blog.categories', [
            'categories' => ArticleCategory::get(),
        ]);
    }

    public function author(Request $request)
    {
        $author = AuthorProfile::firstWhere('url', $request->author);
        
        if (!$author)
        {
            return abort(404);
        }

        return view('static.blog.author', [
            'author' => $author,
        ]);
    }

    public function allAuthors(Request $request)
    {
        return view('static.blog.authors', [
            'authors' => AuthorProfile::get(),
        ]);
    }
}
