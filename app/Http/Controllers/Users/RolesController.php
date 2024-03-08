<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Roles\RoleResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return RoleResource::collection($this->getRoles());
    }

    public function create(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        if ($request->input('role_name') && $request->input('description')) {
            Role::create([
                'name' => strtoupper(trim($request->input('role_name'))),
                'description' => trim($request->input('description')),
                'guard_name' => 'api',
            ]);
        } else {
            abort(402);
        }

        return RoleResource::collection($this->getRoles());
    }

    private function getRoles(): Collection
    {
        return Role::all();
    }
}
