<?php

namespace App\Http\Controllers\Users;

use App\Helpers\Consts;
use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use App\Models\UserCard;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $search = $request->get('search');
        $users = User::withTrashed()
                ->with('userCard')
                ->withSum('balance', 'closing_balance')
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

    public function countriesList(): JsonResponse
    {
        return response()->json(getCountriesForJson());
    }

    public function blockUser(int $user): JsonResponse
    {
        $user = User::withTrashed()->find($user);
        if (!$user->system_account) {
            if ($user->trashed()) {
                $user->restore();
            } else {
                $user->delete();
            }
        }
        return response()->json([], 202);
    }

    public function update(Request $request, User $user): UserResource
    {
        $user->load('userCard');
        $user->update([
           'name' => $request->input('name'),
           'email' => $request->input('email'),
           'first_name' => $request->input('first_name'),
           'last_name' => $request->input('last_name'),
           'country_code' => $request->input('country_code'),
           'is_api_user' => (bool) $request->input('is_api_user'),
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

        if ($request->boolean('card.parts_sale_user')) {
            //only ine user can sell parts for now
            $cards = UserCard::all();
            $cards->each(function($card) {
                $card->update(['parts_sale_user' => false]);
            });
            Cache::put(Consts::DODSON_USER_KEY, $user->id);
        }

        $user->userCard()->update([
            'mobile_phone' => $request->input('card.mobile_phone'),
            'landline_phone' => $request->input('card.landline_phone'),
            'company_name' => $request->input('card.company_name'),
            'trading_name' => $request->input('card.trading_name'),
            'address' => $request->input('card.address'),
            'country' => strtoupper($request->input('card.country')),
            'comment' => $request->input('card.comment'),
            'wholesaler' => $request->boolean('card.wholesaler'),
            'parts_sale_user' => $request->boolean('card.parts_sale_user'),
        ]);

        $user->refresh();
        return new UserResource($user);
    }
}
