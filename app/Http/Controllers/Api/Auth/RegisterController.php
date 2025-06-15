<?php

// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail; // we will create this mail class next
use Carbon\Carbon;


class RegisterController extends Controller
{
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|unique:users,phonenumber',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $otp = rand(100000, 999999);

    $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phonenumber' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => 'unverified',
        'otp_code' => $otp,
        'otp_expires_at' => Carbon::now()->addMinutes(5),
    ]);

    Mail::to($user->email)->send(new SendOtpMail($otp));

    return response()->json(['message' => 'Registered! OTP sent to your email.'], 201);
}
}
