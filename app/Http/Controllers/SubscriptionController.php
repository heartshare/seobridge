<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function goPro(Request $request)
    {
        $plan = SubscriptionPlan::firstWhere('url', $request->plan);

        if (!$plan)
        {
            return abort(404);
        }

        $data = [
            'plan' => $plan,
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
        $request->validate([
            'paymentMethodId' => ['required'],
            'planId' => ['required', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::find($request->planId);

        $request->user()->newSubscription($plan->name, $plan->stripe_price_id)->quantity(null)->create($request->paymentMethodId);
        return 'OK';
    }
}
