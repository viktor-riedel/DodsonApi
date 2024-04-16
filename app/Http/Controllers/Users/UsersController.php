<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $search = $request->get('search');
        $users = User::withTrashed()
                ->orderBy('name')
                ->when($search, function($q) use ($search) {
                    return $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

        return UserResource::collection($users);
    }

    public function edit(User $user): UserResource
    {
        $user->load('userCard');
        if (!$user->userCard) {
            $user->userCard()->create([]);
            $user->refresh();
        }
        return new UserResource($user);
    }

    public function blockUser(int $user): \Illuminate\Http\JsonResponse
    {
        $user = User::withTrashed()->find($user);
        if ($user->trashed()) {
            $user->restore();
        } else {
            $user->delete();
        }

        return response()->json([], 202);
    }

    public function update(Request $request, User $user): UserResource
    {
        $user->refresh();
        $user->update([
           'name' => $request->input('name'),
           'email' => $request->input('email'),
        ]);
        if ($request->input('password')) {
            $user->update(['password' => bcrypt(trim($request->input('password')))]);
        }
        if ($request->input('roles')) {
            $role = Role::where('name', $request->input('roles'))->first();
            $roles = $user->roles;
            foreach ($roles as $assignedRole) {
                $user->removeRole($assignedRole);
            }
            $user->assignRole($role);
        }
        $user->userCard()->update([
            'mobile_phone' => $request->input('card.mobile_phone'),
            'landline_phone' => $request->input('card.landline_phone'),
            'company_name' => $request->input('card.company_name'),
            'trading_name' => $request->input('card.trading_name'),
            'address' => $request->input('card.address'),
            'country' => strtoupper($request->input('card.country')),
            'comment' => $request->input('card.comment'),
            'wholesaler' => $request->input('card.wholesaler'),
        ]);
        return new UserResource($user);
    }
}
