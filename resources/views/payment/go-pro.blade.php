@extends('layouts.static')

@section('title', 'Our pricing: simple, fair and scalable — SEO Bridge')
@section('description', 'We offer SEO tools for businesses of all sizes. Choose your payment plan today!')
@section('page-classes', 'no-header')

@section('content')
<article data-cs-00002>
    <div class="block font-size" data-cs-02001>
        <div class="limiter">
            <h1 style="text-align: center">SEO Bridge {{$plan}}</h1>
        </div>
    </div>

    <div class="limiter">
        @guest
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="returnUrl" value="/go-pro/{{$plan}}">
                <p>
                    <ui-text-input label="Username" name="username" ac="username"></ui-text-input>
                </p>
                <p>
                    <ui-email-input label="Email" name="email"></ui-email-input>
                </p>
                <p>
                    <ui-password-input label="Password" name="password" ac="new-password"></ui-password-input>
                </p>
                <p>
                    <ui-button>Register</ui-button>
                </p>
            </form>

            <h4 style="text-align: center">OR</h4>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="returnUrl" value="/go-pro/{{$plan}}">
                <p>
                    <ui-email-input label="Email" name="email"></ui-email-input>
                </p>
                <p>
                    <ui-password-input label="Password" name="password"></ui-password-input>
                </p>
                <p>
                    <ui-button>Login</ui-button>
                </p>
            </form>
        @endguest

        @auth
            <go-pro-form secret="{{ $intent->client_secret }}"></go-pro-form>
        @endauth
    </div>
</article>
@endsection
