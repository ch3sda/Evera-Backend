<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp_code', $request->otp_code)
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid OTP or email.'], 400);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        $user->role = 'attendee';
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Account verified! You can now log in.']);
    }
    public function resend(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $otp = rand(100000, 999999);
    $user->otp_code = $otp;
    $user->otp_expires_at = Carbon::now()->addMinutes(5);
    $user->save();

    Mail::to($user->email)->send(new SendOtpMail($otp));

    return response()->json(['message' => 'OTP resent']);
}

}
