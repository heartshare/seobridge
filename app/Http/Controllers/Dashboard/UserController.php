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
        return User::find(Auth::id());
    }



    public function changeName(Request $request)
    {
        $request->validate([
            'firstname' => ['present', 'string', 'max:100'],
            'lastname' => ['present', 'string', 'max:100'],
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
}
