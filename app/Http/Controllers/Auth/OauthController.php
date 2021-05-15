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
    public function githubRedirect(Request $request)
    {
        return Socialite::driver('github')->setScopes(['read:user', 'user:email'])->redirect();
    }

    public function githubCallback(Request $request)
    {
        $user = Socialite::driver('github')->user();

        $dbUser = User::firstWhere('email', $user->getEmail());

        // Checks if user with this email already exists
        if ($dbUser)
        {
            // Checks if user with this email is actually an oauth account
            if ($dbUser->is_oauth_user)
            {
                // Checks if user actually used this privider to sign in
                if ($dbUser->oauth->provider === 'github')
                {
                    $dbUser->oauth->token = $user->token;
                    $dbUser->oauth->refresh_token = $user->refreshToken;
                    $dbUser->oauth->save();
                    
                    Auth::login($dbUser);

                    return redirect('/dashboard');
                }
                else
                {
                    return redirect('/login')->withErrors(['oauth_error' => 'Your email already has an account but is not using Github to login.']);
                }
            }
            else
            {
                return redirect('/login')->withErrors(['oauth_error' => 'The email address associated with your Github account is already in use.']);
            }
        }
        else
        {
            $newUser = User::create([
                'email' => $user->getEmail(),
                'username' => $user->getNickname(),
                'email_verified_at' => Carbon::now(),
                'is_oauth_user' => true,
            ]);

            UserOauth::create([
                'user_id' => $newUser->id,
                'type' => 'oauth2',
                'provider' => 'github',
                'token' => $user->token,
                'refresh_token' => $user->refreshToken,
            ]);

            Auth::login($newUser);

            return redirect('/dashboard');
        }
    }
}
