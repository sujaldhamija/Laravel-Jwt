<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::create($validator->validated());
        return response()->json(['message' => 'done', 'user' => $user], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $token = auth()->attempt($validator->validated());
        if ($token) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user()
            ]);
        }
        return response()->json(['error' => 'unauthorised'], 401);
    }

    public function profile(Request $request)
    {
        return response()->json(auth()->user());
    }
    public function logout(Request $request)
    {
        auth()->logout(true);
        return response()->json(['message' => 'user logged out successfully']);
    }
}
