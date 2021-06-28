<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // check validation
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        // create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // create token
        $authToken = $user->createToken('myapitoken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $authToken
        ], 201);
    }

    public function login(Request $request)
    {
        // check validation
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // check email
        $user = User::where('email', $request->email)->first();

        // check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Password Incorrect'
            ], 401);
        }

        // create token
        $authToken = $user->createToken('myapitoken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $authToken
        ], 201);
    }

    public function logout(Request $request)
    {
        // delete token
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You are has been Logged Out!'
        ];
    }
}
