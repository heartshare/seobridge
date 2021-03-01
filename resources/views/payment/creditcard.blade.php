@extends('layouts.static')

@section('content')
<div class="centered-dialog font-size">
    <div class="dialog font-size">
        <form class="block" method="POST" action="{{ route('register') }}">
            <div class="headline">
                <h1>{{ __('Register') }}</h1>
            </div>
        
            <div class="padding font-size">
                @if ($errors->any())
                    <div class="error-container font-size">
                        <div class="title font-size">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                @endif
        
                @csrf
        
                <p>
                    <ui-text-input ac="username" label="{{ __('Username') }}" class="@error('username') invalid @enderror" name="username" autocomplete="username" value="{{ old('username') }}"></ui-text-input>
                </p>
                <p>
                    <ui-email-input ac="email" label="{{ __('Email') }}" class="@error('email') invalid @enderror" name="email" autocomplete="email" value="{{ old('email') }}"></ui-email-input>
                </p>
                <p>
                    <ui-password-input ac="new-password" rating label="{{ __('Password') }}" class="@error('password') invalid @enderror" name="password" autocomplete="new-password"></ui-password-input>
                </p>
            </div>
        
            <div class="button-box clearfix">
                <ui-button style="float: right">{{ __('Register') }}</ui-button>
            </div>
        </form>
    </div>
</div>
@endsection
