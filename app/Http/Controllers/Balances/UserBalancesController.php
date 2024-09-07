<?php

namespace App\Http\Controllers\Balances;

use App\Http\Controllers\Controller;
use App\Http\Resources\CRM\Balances\BalancedUserBalanceResource;
use App\Http\Resources\CRM\Balances\BalancedUserResource;
use App\Http\Resources\CRM\Balances\UserBalanceResource;
use App\Http\Resources\CRM\Balances\UserResource;
use App\Models\User;
use App\Models\UserBalance;
use DB;
use Illuminate\Database\Query\JoinClause;
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

    public function getUserBalance(Request $request, User $user): AnonymousResourceCollection
    {
        $balance = DB::table('user_balances')
            ->selectRaw('
                    user_balances.entity_name,
                    user_balance_items.document_name,
                    user_balance_item_documents.document_description,
                    user_balance_item_documents.amount,
                    case 
		                when user_balance_item_documents.amount is null 
		                then user_balance_items.closing_balance
		                else null 
		            end as closing_balance')
            ->join('user_balance_items', function(JoinClause $join) {
                $join->on('user_balance_items.user_balance_id', '=', 'user_balances.id');
            })
            ->leftJoin('user_balance_item_documents', function(JoinClause $join) {
                $join->on('user_balance_item_documents.user_balance_item_id', '=', 'user_balance_items.id');
            })
            ->where('user_balances.user_id', $user->id)
            ->whereNull('user_balances.deleted_at')
            ->paginate(30);

        return BalancedUserBalanceResource::collection($balance);
    }

    public function getBalancedUser(User $user): BalancedUserResource
    {
        $user->load('balance');
        return new BalancedUserResource($user);
    }
}
