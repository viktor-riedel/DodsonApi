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
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'entity_name' => $this->entity_name,
            'closing_balance' => $this->closing_balance,
            'closing_balance_formatted' => number_format($this->closing_balance),
            'documents' => $this->balance_items_count,
        ];
    }
}
