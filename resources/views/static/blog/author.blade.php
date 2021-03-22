@extends('layouts.static')

@section('title', $author->display_name.' — Author on SEO Bridge')
@section('description', $author->biography)

@section('content')

<header data-cs-00008>
    <h1>Author {{$author->display_name}}</h1>
    <img class="author-image" src="{{$author->image}}" alt="Profile picture of {{$author->display_name}}">
</header>

<div class="limiter">
    <p style="text-align: center">{{$author->biography}}</p>
</div>

@endsection
