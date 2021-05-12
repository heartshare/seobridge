@extends('layouts.static')

@section('title', 'Login - SEO Bridge')
@section('page-classes', 'fullscreen-layout')

@section('content')
<article class="dialog">
    <div class="dialog-wrapper">
        <h1>{{ __('Login') }}</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        @endif

        <div class="flex-wrapper">
            <div class="item">
                <button class="platform-button google">
                    <img class="image" src="/images/app/google_logo_color.svg">
                    <div class="text">Login with Google</div>
                </button>
                <button class="platform-button github">
                    <div class="icon">&#983716;</div>
                    <div class="text">Login with Github</div>
                </button>
                <button class="platform-button facebook">
                    <div class="icon">&#983564;</div>
                    <div class="text">Login with Facebook</div>
                </button>
            </div>
            <div class="divider"></div>
            <div class="item">
                <form class="form-wrapper" method="POST" action="{{ route('login') }}">
                    @csrf
                
                    <ui-email-input name="email" class="@error('email') invalid @enderror" value="{{ old('email') }}" label="{{__('E-Mail Address')}}"></ui-email-input>
                    <ui-password-input name="password" class="@error('password') invalid @enderror" label="{{ __('Password') }}"></ui-password-input>

                    <div class="row">
                        <ui-checkbox style="padding: 0" name="remember" no-border :value="{{ old('remember') ? 'true' : 'false' }}">
                            {{ __('Remember me') }}
                        </ui-checkbox>
                        <div class="spacer"></div>
                        <a href="{{ route('password.request') }}">{{ __('Forgot Password') }}</a>
                    </div>

                    <div class="row margin-top">
                        <a href="/register">Register a new account</a>
                        <div class="spacer"></div>
                        <ui-button>{{ __('Login') }}</ui-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>
@endsection
