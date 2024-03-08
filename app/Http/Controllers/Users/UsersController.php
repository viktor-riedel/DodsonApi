<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = User::orderBy('name')
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

    public function update(Request $request, User $user)
    {
        $user->refresh();
        $user->update([
           'name' => $request->input('name'),
           'email' => $request->input('email'),
        ]);
        if ($request->input('password')) {
            $user->update(['password' => bcrypt(trim($request->input('password')))]);
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
