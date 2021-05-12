@extends('layouts.static')

@section('title', 'Forgot password - SEO Bridge')
@section('page-classes', 'fullscreen-layout')

@section('content')
<article class="dialog">
    <div class="dialog-wrapper">
        <h1>{{ __('Reset password') }}</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        @endif

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex-wrapper">
            <div class="item">
                <form class="form-wrapper" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <ui-email-input name="email" class="@error('email') invalid @enderror" value="{{ old('email') }}" label="{{__('Reset Password E-Mail Address')}}"></ui-email-input>

                    <div class="row margin-top">
                        <div class="spacer"></div>
                        <ui-button>{{ __('Send reset link') }}</ui-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>
@endsection
