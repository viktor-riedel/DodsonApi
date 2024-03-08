<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;

class UsersController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = User::orderBy('name')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

        return UserResource::collection($users);
    }

    public function edit(User $user)
    {
        return response()->json($user);
    }
}
