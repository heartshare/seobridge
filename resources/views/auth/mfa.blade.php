@extends('layouts.static')

@section('title', '2 Factor Authentication - SEO Bridge')
@section('page-classes', 'fullscreen-layout')

@section('content')
<article class="dialog">
    <div class="dialog-wrapper">
        <h1>{{ __('2 Factor Authentication') }}</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        @endif

        <div class="flex-wrapper">
            <div class="item">
                <form class="form-wrapper" method="POST" action="/verify-mfa">
                    @csrf
                
                    <ui-text-input name="secret" class="@error('secret') invalid @enderror" value="{{ old('secret') }}" label="{{__('Passcode')}}"></ui-text-input>

                    <div class="row margin-top">
                        <div class="spacer"></div>
                        <ui-button>{{ __('Verify') }}</ui-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>
@endsection
