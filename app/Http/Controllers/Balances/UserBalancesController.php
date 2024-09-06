<?php

namespace App\Http\Controllers\Balances;

use App\Http\Controllers\Controller;
use App\Http\Resources\CRM\Balances\UserBalanceResource;
use App\Http\Resources\CRM\Balances\UserResource;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserBalancesController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $user = $request->get('user');

        $balances = UserBalance::with('user')
            ->whereHas('user', function($query) {
                return $query->whereHas('roles', function ($query) {
                    $query->where('roles.name', 'USER');
                });
            })
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', $user);
            })
            ->paginate(30);

        return UserBalanceResource::collection($balances);
    }

    public function listUsers(): AnonymousResourceCollection
    {
        $users = User::with('balance')
            ->whereHas('roles', function ($query) {
                $query->where('roles.name', 'USER');
            })
            ->orderBy('name')
            ->get();

        return UserResource::collection($users);
    }
}
