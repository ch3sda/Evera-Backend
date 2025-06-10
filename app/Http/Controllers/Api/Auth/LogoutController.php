<?php

// app/Http/Controllers/Auth/LogoutController.php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
    
            if (!$user) {
                return response()->json(['message' => 'No authenticated user'], 401);
            }
    
            $token = $user->currentAccessToken();
    
            if (!$token) {
                return response()->json(['message' => 'No access token found'], 401);
            }
    
            $token->delete();
    
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout failed', 'details' => $e->getMessage()], 500);
        }
    }
}
