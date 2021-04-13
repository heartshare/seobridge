@extends('layouts.static')

@section('title', $article->title)
@section('description', $article->intro_text)
@section('page-classes', 'no-header')

@section('content')

<article data-cs-01101>
    <div class="limiter font-size">
        <div class="info-side"></div>
        <div class="article-limiter font-size">
            <h1 class="article-headline">{{$article->title}}</h1>
            
            <div class="article-info">
                <div class="author">
                    <img class="author-image" src="{{$article->author->image}}" alt="{{$article->author->display_name}} profile image">
                    <a class="author-link" href="/resources/author/{{$article->author->url}}" rel="author">{{$article->author->display_name}}</a>
                </div>
                @if ($article->category)
                    <span class="divider text">•</span>
                    <a class="category-link" href="/resources/category/{{$article->category->url}}">{{$article->category->name}}</a>
                @endif
                @if ($article->published_at)
                    <span class="divider text">•</span>
                    <span class="text">{{$article->published_at->format('F d, Y')}}</span>
                @endif
            </div>

            @if ($article->intro_image)
                <img class="article-preview-image" src="{{$article->intro_image}}" alt="">
            @endif

            @if ($article->intro_text)
                <p>{{$article->intro_text}}</p>
            @endif
    
            <div>{!!$article->full_text!!}</div>
        </div>
        <div class="annotations"></div>
    </div>
</article>

@endsection
