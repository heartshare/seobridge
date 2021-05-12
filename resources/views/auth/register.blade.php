@extends('layouts.static')

@section('title', 'Register - SEO Bridge')
@section('page-classes', 'fullscreen-layout')

@section('content')
<article class="dialog">
    <div class="dialog-wrapper">
        <h1>{{ __('Register') }}</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        @endif

        <div class="flex-wrapper">
            <div class="item">
                <button class="platform-button google">
                    <img class="image" src="/images/app/google_logo_color.svg">
                    <div class="text">Register with Google</div>
                </button>
                <button class="platform-button github">
                    <div class="icon">&#983716;</div>
                    <div class="text">Register with Github</div>
                </button>
                <button class="platform-button facebook">
                    <div class="icon">&#983564;</div>
                    <div class="text">Register with Facebook</div>
                </button>
            </div>
            <div class="divider"></div>
            <div class="item">
                <form class="form-wrapper" method="POST" action="{{ route('register') }}">
                    @csrf
                
                    <ui-text-input ac="username" label="{{ __('Username') }}" class="@error('username') invalid @enderror" name="username" autocomplete="username" value="{{ old('username') }}"></ui-text-input>
                    <ui-email-input ac="email" label="{{ __('Email') }}" class="@error('email') invalid @enderror" name="email" autocomplete="email" value="{{ old('email') }}"></ui-email-input>
                    <ui-password-input ac="new-password" rating label="{{ __('Password') }}" class="@error('password') invalid @enderror" name="password" autocomplete="new-password"></ui-password-input>

                    <div class="row">
                        <ui-checkbox style="padding: 0" name="remember" no-border>
                            I have read and accept the <a href="/privacy-policy" target="_blank">privacy policy</a>.
                        </ui-checkbox>
                    </div>

                    <div class="row margin-top">
                        <a href="/login">I already have an account</a>
                        <div class="spacer"></div>
                        <ui-button>{{ __('Register Now') }}</ui-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>
@endsection
