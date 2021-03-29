@extends('layouts.static')

@section('title', 'SEO knowledge base — SEO Bridge')
@section('description', 'Helpfull tips, tricks and general knowledge about search engine optimizing. Learn about the ins and outs of SEO.')

@section('content')
<header data-cs-00005>
    <h1>SEO resources and knowledge</h1>
</header>

@foreach ($articles as $article)
    <article @if ($loop->index < 1) data-cs-01001 @else data-cs-01002 @endif>
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
                    @if ($article->category)
                        <span class="divider text">•</span>
                        <a class="category-link" href="/resources/category/{{$article->category->url}}">{{$article->category->name}}</a>
                    @endif
                    @if ($article->published_at)
                        <span class="divider text">•</span>
                        <span class="text">{{$article->published_at->format('F d, Y')}}</span>
                    @endif
                </div>
        
                <h2 class="article-headline">{{$article->title}}</h2>
        
                <blockquote class="article-preview-text" cite="/resources/{{$article->url}}">{{$article->intro_text}}</blockquote>
        
                <a class="read-more-button" href="/resources/{{$article->url}}">Read more</a>
            </div>
        </div>
    </article>
    
@endforeach
@endsection
