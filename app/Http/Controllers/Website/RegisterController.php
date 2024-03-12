<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\RegisterRequest;
use App\Mail\UserRegisteredMail;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('first_name') . ' ' . $request->validated('last_name'),
            'email' => $request->validated('user_email'),
            'password' => bcrypt($request->validated('password')),
        ]);

        \Mail::to($user->email)->send(new UserRegisteredMail($user));

        return response()->json(['success' => true], 201);
    }
}
