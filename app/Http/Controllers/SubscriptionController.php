<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function goPro(Request $request)
    {
        $data = [
            'plan' => $request->plan,
            'intent' => null,
        ];

        if ($request->user())
        {
            $data['intent'] = $request->user()->createSetupIntent();
        }

        return view('payment.go-pro', $data);
    }

    public function complete(Request $request)
    {
        $request->user()->newSubscription('Pro-Plan', 'price_1IQFunDa1TGHitv5WZXTvjF7')->quantity(null)->create($request->paymentMethodId);
        return 'OK';
    }
}
