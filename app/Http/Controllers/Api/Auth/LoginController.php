<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
public function login(Request $request)
{
    // Validate input (email & password)
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'remember' => 'boolean',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $remember = $request->boolean('remember', false);

    // Create token with expiry depending on 'remember me'
    $tokenResult = $user->createToken('authToken', [], now()->addMinutes($remember ? 43200 : 60)); // 30 days or 1 hour
    $token = $tokenResult->plainTextToken;

    // Explicitly save expires_at (Sanctum 3.x)
    $tokenResult->accessToken->expires_at = $remember ? now()->addDays(30) : now()->addHour();
    $tokenResult->accessToken->save();

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'expires_at' => $tokenResult->accessToken->expires_at,
        'user' => $user,
    ]);
}

}