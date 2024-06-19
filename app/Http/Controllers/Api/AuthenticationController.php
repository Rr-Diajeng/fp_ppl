<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticationController extends Controller
{
    public function login(Request $request){
        $request -> validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Invalid credentials.',
                ],
                'data' => null,
            ], 401);
        }

        $token = $user->createToken($request->email)->plainTextToken;
        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Successful login.',
            ],
            'data' => [
                'token' => $token,
                'userData' => [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ],
            ],
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Successful Logout.',
            ],
            'data' => null,
        ], 200);
    }
}
