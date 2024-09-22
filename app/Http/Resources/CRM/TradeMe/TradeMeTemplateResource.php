<?php

namespace App\Http\Resources\CRM\TradeMe;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeMeTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'title' => $this->title ?? null,
            'short_description' => $this->short_description ?? null,
            'description' => $this->description ?? null,
            'delivery_options' => $this->delivery_options_array ?? [],
            'default_duration' => $this->default_duration ?? null,
            'payments_options' => $this->payment_options_array ?? [],
            'update_prices' => $this->update_prices ?? false,
            'relist' => $this->relist ?? false,
        ];
    }
}
