<?php

namespace App\Http\Controllers\Api\Attendee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrganizerRequest;
use Illuminate\Support\Facades\Auth;

class OrganizerRequestController extends Controller
{
public function request(Request $request)
{
    $request->validate([
        'reason' => 'required|string',
    ]);

    $userId = Auth::id();
    $existing = OrganizerRequest::where('user_id', $userId)->first();

    if ($existing) {
        $existing->reason = $request->reason;
        $existing->status = 'pending';
        $existing->request_attempt += 1;
        $existing->save();
    } else {
        OrganizerRequest::create([
            'user_id' => $userId,
            'reason' => $request->reason,
            'status' => 'pending',
            'request_attempt' => 1,
        ]);
    }

    return response()->json(['message' => 'Request submitted successfully.']);
}

}
