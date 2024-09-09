<?php

namespace App\Http\Resources\CRM\Balances;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalancedUserBalanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'entity_name' => $this->entity_name,
            'closing_balance' => $this->closing_balance,
            'payment' => $this->amount,
            'document_description' => $this->document_description,
            'document_name' => $this->document_name,
        ];
    }
}
