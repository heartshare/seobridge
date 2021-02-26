<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getAllNotifications(Request $request)
    {
        return UserNotification::where('user_id', Auth::id())->get();
    }
}
