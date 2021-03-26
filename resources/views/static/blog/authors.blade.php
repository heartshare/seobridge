@extends('layouts.static')

@section('title', 'SEO knowledge base — SEO Bridge')
@section('description', 'Helpfull tips, tricks and general knowledge about search engine optimizing. Learn about the ins and outs of SEO.')

@section('content')
<header data-cs-00005>
    <h1>All authors on SEO Bridge</h1>
</header>

<div class="limiter">
    @foreach ($authors as $author)
        <h2>{{$author->display_name}}</h2>
        <p>
            {{$author->biography}}<br>
            <a href="/resources/author/{{$author->url}}">View Profile</a>
        </p>
    @endforeach
</div>
@endsection
