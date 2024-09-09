<?php

namespace App\Http\Resources\CRM\Balances;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalancedUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'closing_balance' => number_format($this->balance->sum('closing_balance')),
        ];
    }
}
