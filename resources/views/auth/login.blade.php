@extends('layouts.static')

@section('content')
<div class="centered-dialog font-size">
    <div class="dialog font-size">

        <form class="block" method="POST" action="{{ route('login') }}">

            <div class="headline font-size">
                <h1>{{ __('Login') }}</h1>
            </div>
        
            <div class="padding font-size">
                @if ($errors->any())
                    <div class="error-container">
                        <div class="title">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                @endif
            
                @csrf
            
                <p>
                    <ui-email-input name="email" class="@error('email') invalid @enderror" value="{{ old('email') }}" label="{{__('E-Mail Address')}}"></ui-email-input>
                </p>
                <p>
                    <ui-password-input name="password" class="@error('password') invalid @enderror" label="{{ __('Password') }}"></ui-password-input>
                </p>
            
                <p>
                    <ui-checkbox name="remember" no-border :value="{{ old('remember') ? 'true' : 'false' }}">
                        {{ __('Remember me') }}
                    </ui-checkbox>
                </p>
            </div>
        
            <div class="button-box clearfix">
                @if (Route::has('password.request'))
                    <ui-button light href="{{ route('password.request') }}" icon="none">{{ __('Forgot Your Password?') }}</ui-button>
                @endif
                
                <ui-button style="float: right" icon="none">{{ __('Login') }}</ui-button>
            </div>
        </form>
    </div>
</div>
@endsection
