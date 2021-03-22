@extends('layouts.static')

@section('title', 'All articles in: '.$category->description.' — SEO Bridge')
@section('description', $category->description)

@section('content')
<header data-cs-00005>
    <h1>Articles in: {{$category->name}}</h1>
</header>

@foreach ($articles as $article)
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
                    <span class="text">{{$article->published_at->format('F d, Y')}}</span>
                </div>
        
                <h2 class="article-headline">{{$article->title}}</h2>
        
                <blockquote class="article-preview-text" cite="/resources/{{$article->url}}">{{$article->intro_text}}</blockquote>
        
                <a class="read-more-button" href="/resources/{{$article->url}}">Read more</a>
            </div>
        </div>
    </article>
    
@endforeach
@endsection
