<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserMFAMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class MFAController extends Controller
{
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
