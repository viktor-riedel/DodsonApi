<?php

namespace App\Http\Resources\CRM\Balances;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBalanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'closing_balance' => (int) $this->balance_sum_closing_balance,
            'closing_balance_formatted' => number_format((int) $this->balance_sum_closing_balance),
            'documents' => (int) $this->balance_sum_balance_items_count,
        ];
    }
}
