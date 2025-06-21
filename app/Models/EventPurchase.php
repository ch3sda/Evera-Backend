<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPurchase extends Model
{
    public function storePurchase(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'payment_intent_id' => 'required|string',
    ]);

    $user = $request->user();

    // Check if this user already bought it (optional)
    $alreadyPurchased = \App\Models\EventPurchase::where('user_id', $user->id)
        ->where('event_id', $request->event_id)
        ->exists();

    if ($alreadyPurchased) {
        return response()->json(['message' => 'Event already purchased.'], 400);
    }

    \App\Models\EventPurchase::create([
        'user_id' => $user->id,
        'event_id' => $request->event_id,
        'payment_intent_id' => $request->payment_intent_id,
    ]);

    return response()->json(['message' => 'Ticket purchased successfully.']);
}

}
