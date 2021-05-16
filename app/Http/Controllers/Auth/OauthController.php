<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOauth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OauthController extends Controller
{
    public function googleRedirect(Request $request)
    {
        return Socialite::driver('google')->redirect();
    }

    public function githubRedirect(Request $request)
    {
        return Socialite::driver('github')->setScopes(['read:user', 'user:email'])->redirect();
    }

    public function facebookRedirect(Request $request)
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback(Request $request)
    {
        $user = Socialite::driver($request->provider)->user();

        $dbUser = User::firstWhere('email', $user->getEmail());

        // Checks if user with this email already exists
        if ($dbUser)
        {
            // Checks if user with this email is actually an oauth account
            if ($dbUser->is_oauth_user)
            {
                // Checks if user actually used this privider to sign in
                if ($dbUser->oauth->provider === $request->provider)
                {
                    $dbUser->oauth->token = $user->token;
                    $dbUser->oauth->refresh_token = $user->refreshToken;
                    $dbUser->oauth->save();
                    
                    Auth::login($dbUser);

                    return redirect('/dashboard');
                }
                else
                {
                    return redirect('/login')->withErrors(['oauth_error' => 'You already have an account but you did not use ' . $request->provider . ' to register it. Try a different platform.']);
                }
            }
            else
            {
                return redirect('/login')->withErrors(['oauth_error' => 'The email address of your ' . $request->provider . ' account is already in use. Perhaps you signed up via email and password?']);
            }
        }
        else
        {
            $newUser = User::create([
                'email' => $user->getEmail(),
                'username' => $user->getEmail(),
                'email_verified_at' => Carbon::now(),
                'is_oauth_user' => true,
            ]);

            UserOauth::create([
                'user_id' => $newUser->id,
                'type' => $request->type,
                'provider' => $request->provider,
                'token' => $user->token,
                'refresh_token' => $user->refreshToken,
            ]);

            Auth::login($newUser);

            return redirect('/dashboard');
        }
    }
}
