<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Roles\RolePermissionsResource;
use Spatie\Permission\Models\Role;

class RolesPermissionsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $rolePermissions = Role::with('permissions')->get();
        return RolePermissionsResource::collection($rolePermissions);
    }
}
