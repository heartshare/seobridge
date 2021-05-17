<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMFAMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return User::where('id', Auth::id())->with(['profile_image', 'mfa_methods'])->first();
    }



    public function changeName(Request $request)
    {
        $request->validate([
            'firstname' => ['present', 'max:100'],
            'lastname' => ['present', 'max:100'],
        ]);

        $user = User::find(Auth::id());

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->save();

        return [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
        ];
    }



    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
            'newPassword' => ['required', 'string', 'min:8'],
        ]);

        $user = User::find(Auth::id());

        $login['email'] = $user->email;
        $login['password'] = $request->password;

        if( !Auth::attempt($login) )
        {
            return response('UNAUTHORIZED', 403);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response('OK');
    }



    public function closeAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::find(Auth::id());

        $login['email'] = $user->email;
        $login['password'] = $request->password;

        if( !Auth::attempt($login) )
        {
            return response('UNAUTHORIZED', 403);
        }

        $user->delete();

        return response('OK');
    }



    public function setupTOTPMFA(Request $request)
    {
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey(64);

        $method = UserMFAMethod::firstOrCreate([
            'user_id' => Auth::id(),
            'type' => 'TOTP',
        ],[
            'token' => $secret,
            'name' => 'App Authenticator'
        ]);

        return $google2fa->getQRCodeUrl(
            'SEO Bridge',
            Auth::user()->email,
            $method->token
        );
    }

    public function verifyTOTPMFA(Request $request)
    {
        $request->validate([
            'secret' => ['required', 'string'],
        ]);
        
        $method = UserMFAMethod::where('user_id', Auth::id())->firstWhere('type', 'TOTP');
        
        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($method->token, $request->secret))
        {
            return response('SECRET_INVALID', 422);
        }

        $method->is_verified = true;
        $method->save();

        return response('OK');
    }

    public function setMFAStatus(Request $request)
    {
        $request->validate([
            'status' => ['required', 'boolean'],
        ]);

        $user = User::find(Auth::id());
        $user->is_mfa_enabled = $request->status;
        $user->save();

        return response('OK');
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

        session(['fully_authenticated' => true]);

        return redirect(session('returnURL') ? session('returnURL') : '/dashboard');
    }
}
