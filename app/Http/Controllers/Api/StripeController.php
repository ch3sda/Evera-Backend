<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod as StripePaymentMethod;

class StripeController extends Controller
{
    public function storePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = $request->user();

        // Create Stripe customer if needed (optional)
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Attach payment method to customer
        $paymentMethod = StripePaymentMethod::retrieve($request->payment_method_id);
        $paymentMethod->attach([
            'customer' => $user->stripe_customer_id,
        ]);

        // Save safe card info to database
        PaymentMethod::create([
            'user_id' => $user->id,
            'brand' => $paymentMethod->card->brand,
            'last4' => $paymentMethod->card->last4,
            'exp_month' => $paymentMethod->card->exp_month,
            'exp_year' => $paymentMethod->card->exp_year,
            'stripe_pm_id' => $paymentMethod->id,
        ]);

        return response()->json(['message' => 'Card saved successfully.']);
    }
}
