<?php

// app/Http/Controllers/Api/Auth/ProfileController.php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->role === 'unverified') {
            return response()->json([
                'message' => 'Account is not verified. Please check your email for the OTP.'
            ], 403);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email ?? '',
            'role' => $user->role ?? '',
            'phonenumber' => $user->phonenumber ?? '',
        ]);
    }
}

