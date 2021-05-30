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
        $request->user()->createOrGetStripeCustomer();

        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }

    

    public function getAllPaymentMethods(Request $request)
    {
        $stripeUser = $request->user()->createOrGetStripeCustomer();

        return [
            'methods' => $request->user()->paymentMethods(),
            'default' => $stripeUser->invoice_settings->default_payment_method,
        ];
    }



    public function getAllSubscriptions(Request $request)
    {
        return $request->user()->asStripeCustomer()->subscriptions;
    }



    public function getSetupIntent(Request $request)
    {
        $request->user()->createOrGetStripeCustomer();

        return $request->user()->createSetupIntent();
    }



    public function addPaymentMethodToUser(Request $request)
    {
        $request->user()->createOrGetStripeCustomer();

        return $request->user()->addPaymentMethod($request->paymentMethod);
    }



    public function setDefaultPaymentMethod(Request $request)
    {
        $request->user()->updateDefaultPaymentMethod($request->paymentMethod);

        return $request->paymentMethod;
    }



    public function deletePaymentMethod(Request $request)
    {
        $request->user()->findPaymentMethod($request->paymentMethod)->delete();

        return $request->paymentMethod;
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
