<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return User::where('id', Auth::id())->with('profile_image')->first();
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
}
