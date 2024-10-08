<?php

namespace App\Http\Controllers\Auth;

use App\Events\User\LoginSuccessEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RestorePasswordRequest;
use App\Http\Traits\CartTrait;
use App\Jobs\Auth\ResetPasswordJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use CartTrait;

    public function login(AuthRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $password = $request->validated('password');
        if (!auth()->attempt(['email' => $email, 'password' => $password])) {
            return response()->json(['message' => 'invalid credentials'], 401);
        }

        $user = User::where('email', $email)->firstOrFail();

        $this->checkCartExist($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        $request->session()->regenerate();
        if (!$user->is_api_user) {
            event(new LoginSuccessEvent(
                    $request->user()->id, 'Login',
                    'Login successful '.PHP_EOL.'Last login was: ' .
                    (
                    $request->user()->last_login_at ?
                        Carbon::parse($request->user()->last_login_at)->format('d/m/Y')
                        : now()->format('d/m/Y')
                    ),
                    'success')
            );
        }
        $user->update(['last_login_at' => now()]);
        $userRole = $user->getRoleNames()->first();
        if (!$userRole) {
            $user->assignRole('USER');
        }

        if ($user->is_api_user) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        return response()->json([
           'name' => $user->name,
           'email' => $user->email,
           'access_token' => $token,
           'token_type' => 'Bearer',
           'is_api_user' => $user->is_api_user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'id' => $request->user()->id,
                'api_user' => $request->user()->is_api_user,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->getRoleNames()->first(),
                'country_code' => $request->user()->country_code,
                'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name')->toArray(),
                'wholesaler' => $request->user()->userCard?->wholesaler,
                'user_balance' => number_format($request->user()->balance()->sum('closing_balance')),
            ]);
        }
        return response()->json(['message' => 'invalid credentials'], 401);
    }

    public function forgetPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $resetCode = \Str::uuid()->toString();
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['reset_code' => $resetCode]);
            ResetPasswordJob::dispatch($user);
        }
        return response()->json($email);
    }

    public function restorePassword(RestorePasswordRequest $request): JsonResponse
    {
        $uuid = $request->validated('guid');
        $newPassword = bcrypt($request->validated('new_password'));
        $user = User::where('reset_code', $uuid)->first();
        if ($user) {
            $user->update(['password' => $newPassword, 'reset_code' => null]);
        }

        return response()->json([], 203);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        return response()->json([], 204);
    }
}
