<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

        if (!Auth::attempt($credentials, ($request->remember || false)))
        {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
        
        $request->session()->regenerate();

        return redirect($request->returnUrl ? $request->returnUrl : RouteServiceProvider::HOME);
    }
}
