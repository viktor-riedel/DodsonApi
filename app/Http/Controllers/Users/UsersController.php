<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;

class UsersController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = User::with('userInformation')
                ->with('trashed')
                ->with('roles', 'roles.permissions')
                ->orderBy('name', 'asc')
                ->get();

        return UserResource::collection($users);
    }

    public function create()
    {

    }
}
