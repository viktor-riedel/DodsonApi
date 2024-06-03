<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\RegisterRequest;
use App\Jobs\Auth\RegistrationJob;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('first_name') . ' ' . $request->validated('last_name'),
            'email' => $request->validated('user_email'),
            'password' => bcrypt($request->validated('password')),
            'last_login_at' => now(),
        ]);

        $user->assignRole(['USER']);
        $user->cart()->create([]);

        RegistrationJob::dispatch($user);

        return response()->json(['success' => true], 201);
    }
}
