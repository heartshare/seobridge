@extends('layouts.static')

@section('title', 'SEO knowledge base — SEO Bridge')
@section('description', 'Helpfull tips, tricks and general knowledge about search engine optimizing. Learn about the ins and outs of SEO.')

@section('content')
<header data-cs-00005>
    <h1>All categories on SEO Bridge</h1>
</header>

<div class="limiter">
    @foreach ($categories as $category)
        <h2>{{$category->name}}</h2>
        <p>
            {{$category->description}}<br>
            <a href="/resources/category/{{$category->url}}">See all articles</a>
        </p>
    @endforeach
</div>
@endsection
