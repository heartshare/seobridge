<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMFAMethod;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::firstWhere('email', $request->email);

        if ($user && $user->is_oauth_user)
        {
            return back()->withErrors([
                'oauth_error' => 'You have logged in with a platform like Google or Facebook. Please login using them.',
            ]);
        }

        if (Auth::attempt($credentials, ($request->remember || false)))
        {
            $request->session()->regenerate();

            if ($request->returnUrl) $this->redirectTo = $request->returnUrl;
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }



    public function TOTPMFA(Request $request)
    {
        $request->validate([
            'secret' => ['required', 'string', 'max:6', 'min:6'],
        ]);

        $method = UserMFAMethod::where('user_id', Auth::id())->firstWhere('type', 'TOTP');
        
        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($method->token, $request->secret))
        {
            return back()->withErrors([
                'oauth_error' => 'Your code was invalid',
            ]);
        }

        session(['mfa' => true]);

        return redirect(session('returnURL') ? session('returnURL') : '/dashboard');
    }
}
