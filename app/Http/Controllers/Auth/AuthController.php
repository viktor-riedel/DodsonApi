<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(AuthRequest $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->validated('email');
        $password = $request->validated('password');
        if (!auth()->attempt(['email' => $email, 'password' => $password])) {
            return response()->json(['message' => 'invalid credentials'], 401);
        }
        $user = User::where('email', $email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
           'name' => $user->name,
           'email' => $user->email,
           'access_token' => $token,
           'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ]);
        }
        return response()->json(['message' => 'invalid credentials'], 401);
    }
}
