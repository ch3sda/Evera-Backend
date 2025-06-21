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
    public function createPaymentIntent(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
    ]);

    $user = $request->user();
    $event = Event::findOrFail($request->event_id);

    Stripe::setApiKey(env('STRIPE_SECRET'));

    // Optional: create customer if needed
    if (!$user->stripe_customer_id) {
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
        ]);
        $user->stripe_customer_id = $customer->id;
        $user->save();
    }

    // Calculate price + VAT (10%, max $5)
    $baseAmount = $event->price * 100; // in cents
    $vat = min($baseAmount * 0.10, 500); // max $5 = 500 cents
    $totalAmount = intval(round($baseAmount + $vat));

    // Create PaymentIntent
    $intent = PaymentIntent::create([
        'amount' => $totalAmount,
        'currency' => 'usd',
        'customer' => $user->stripe_customer_id,
        'description' => 'Ticket purchase: ' . $event->title,
        'metadata' => [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ],
    ]);

    return response()->json([
        'clientSecret' => $intent->client_secret,
        'amount' => $totalAmount / 100,
        'vat' => $vat / 100,
        'event_title' => $event->title,
    ]);
}
}
