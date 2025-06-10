<?php

// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phonenumber',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'in:user,organizer,admin',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the new user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phonenumber' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        // Optionally, you can log the user in after registration
        // auth()->login($user);

        return response()->json(['message' => 'User successfully registered'], 201);
    }
}
