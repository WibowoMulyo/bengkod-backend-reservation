<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        Log::error('Login failed for email: ' . $request->email);
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    Log::info('Login successful for user: ' . Auth::user()->email);

    // Generate JWT token
    $token = JWTAuth::attempt($credentials);

    return response()->json([
        'success' => true,
        'token' => $token,
    ], 200);
}

}
