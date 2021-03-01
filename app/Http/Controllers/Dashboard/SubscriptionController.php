<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function redirectToBillingPortal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }

    public function getSetupIntent(Request $request)
    {
        return $request->user()->createSetupIntent();
    }

    public function testSub(Request $request)
    {
        if ($request->user()->hasDefaultPaymentMethod()) {
            $request->user()->newSubscription(
                'Pro-Plan', 'price_1IQFunDa1TGHitv5WZXTvjF7'
            )->quantity(null)->create($request->user()->defaultPaymentMethod());
        }
        else
        {
            return $request->user()->paymentMethods();
            return response('NO_PAYMENT_METHOD', 403);
        }
    }
}
