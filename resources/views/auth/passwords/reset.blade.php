@extends('layouts.static')

@section('title', 'Reset password - SEO Bridge')
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
                <form class="form-wrapper" method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <ui-email-input name="email" ac="email" class="@error('email') invalid @enderror" value="{{ $email ?? old('email') }}" label="{{__('Reset Password E-Mail Address')}}"></ui-email-input>
                    <ui-password-input name="password" ac="new-password" class="@error('password') invalid @enderror" label="{{ __('Password') }}"></ui-password-input>
                    <ui-password-input name="password_confirmation" ac="new-password" label="{{ __('Confirm password') }}"></ui-password-input>

                    <div class="row margin-top">
                        <div class="spacer"></div>
                        <ui-button>{{ __('Reset Password') }}</ui-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>
@endsection
