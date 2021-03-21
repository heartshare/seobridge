@extends('layouts.static')

@section('title', $article->title)
@section('description', $article->intro_text)

@section('content')
<header data-cs-00005>
    <h1>{{$article->title}}</h1>
</header>

<article data-cs-01002>
    <div class="limiter">
        <div class="article-preview-image-wrapper">
            <img class="article-preview-image" src="{{$article->intro_image}}" alt="">
        </div>
        
        <div class="article-preview-wrapper">
            <div class="article-info">
                <div class="author">
                    <img class="author-image" src="{{$article->author->image}}" alt="{{$article->author->display_name}} profile image">
                    <a class="author-link" href="/resources/author/{{$article->author->url}}" rel="author">{{$article->author->display_name}}</a>
                </div>
                <span class="divider text">•</span>
                <a class="category-link" href="/resources/category/{{$article->category->url}}">{{$article->category->name}}</a>
                <span class="divider text">•</span>
                <span class="text">{{$article->published_at}}</span>
            </div>
    
            <div class="article-preview-text">{{$article->intro_text}}</div>
            <div class="article-preview-text">{{$article->full_text}}</div>
        </div>
    </div>
</article>

@endsection
