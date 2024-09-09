<?php

namespace App\Http\Controllers\UserPanel\Balance;

use App\Http\Controllers\Controller;
use App\Http\Resources\CRM\Balances\BalancedUserBalanceResource;
use DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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
            ->where('user_balances.user_id', $request->user()->id)
            ->whereNull('user_balances.deleted_at')
            ->paginate(30);

        return BalancedUserBalanceResource::collection($balance);
    }
}
