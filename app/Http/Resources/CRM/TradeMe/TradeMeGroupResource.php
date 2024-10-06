<?php

namespace App\Http\Resources\CRM\TradeMe;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeMeGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->group_name,
            'note' => $this->note,
            'path' => $this->trade_me_path,
            'user' => $this->user->name,
        ];
    }
}
