<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrganizerRequest; // ✅ correct model
use Illuminate\Http\Request;

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrganizerRequest;
use App\Mail\RequestRejectedMail;
use App\Mail\RequestApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function approveOrganizerRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $user = User::findOrFail($request->user_id);

        $organizerRequest = OrganizerRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$organizerRequest) {
            return response()->json(['message' => 'No request found.'], 404);
        }

        $organizerRequest->status = $request->status;
        $organizerRequest->save();

        if ($request->status === 'approved') {
            $user->role = 'organizer';
            $user->save();
            Mail::to($user->email)->send(new RequestApprovedMail($user));
        }

        if ($request->status === 'rejected') {
            Mail::to($user->email)->send(new RequestRejectedMail($user));
        }

        return response()->json(['message' => 'Request updated.']);
    }
}
