<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RestorePasswordRequest;
use App\Mail\ResetPasswordMail;
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
        if ($user->hasRole('USER')) {
            abort(401, 'Users login is not allowed at the moment');
        }
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
                'role' => $request->user()->getRoleNames()->first(),
                'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name')->toArray(),
            ]);
        }
        return response()->json(['message' => 'invalid credentials'], 401);
    }

    public function forgetPassword(ForgotPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->validated('email');
        $resetCode = \Str::uuid()->toString();
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['reset_code' => $resetCode]);
            \Mail::to($user->email)->send(new ResetPasswordMail($user));
        }
        return response()->json($email);
    }

    public function restorePassword(RestorePasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $uuid = $request->validated('guid');
        $newPassword = bcrypt($request->validated('new_password'));
        $user = User::where('reset_code', $uuid)->first();
        if ($user) {
            $user->update(['password' => $newPassword, 'reset_code' => null]);
        }

        return response()->json([], 203);
    }
}
