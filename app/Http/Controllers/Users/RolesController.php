<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Roles\RoleResource;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $roles = Role::all();
        return RoleResource::collection($roles);
    }
}
