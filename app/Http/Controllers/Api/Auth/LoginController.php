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
        // Validate the request
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to authenticate the user
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Login attempt failed for email: ' . $request->email);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Check if the user is active (optional, if your app has an 'active' field)
        if (!$user->active) {
            Log::info('Inactive user attempted login: ' . $user->email);
            return response()->json(['message' => 'Account is inactive'], 403);
        }

        // Create a token
        $token = $user->createToken('auth_token')->plainTextToken;
        Log::info('Generated Token: ' . $token);
        // Return a successful response
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