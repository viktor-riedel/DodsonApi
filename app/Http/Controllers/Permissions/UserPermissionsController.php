<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use App\Http\Resources\Roles\RoleResource;
use Spatie\Permission\Models\Role;

class UserPermissionsController extends Controller
{
    public function getUserRoles()
    {
        $roles = Role::get();
        return RoleResource::collection($roles);
    }
}
