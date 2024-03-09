<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Roles\RolePermissionsResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $rolePermissions = Role::with('permissions')->get();
        return RolePermissionsResource::collection($rolePermissions);
    }

    public function assign(Request $request): \Illuminate\Http\JsonResponse
    {
        $role = Role::find($request->input('role_id'));
        if ($role) {
            $permissions = Permission::whereIn('name', collect($request->input('permissions'))->pluck('name')->toArray())->get();
            $role->syncPermissions($permissions);
        }
        return response()->json([], 202);
    }
}
