<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['nullable', 'boolean'],
        ]);

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Login attempt failed for email: ' . $request->email);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // if (!$user->active) {
        //     return response()->json(['message' => 'Account is inactive'], 403);
        // }

        if ($user->role === 'unverified') {
            return response()->json([
                'message' => 'Account is not verified. Please check your email for the OTP.'
            ], 403);
        }

        // Use long-lived token name if "remember" is true
        $tokenName = $request->remember ? 'auth_token_remembered' : 'auth_token';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

}