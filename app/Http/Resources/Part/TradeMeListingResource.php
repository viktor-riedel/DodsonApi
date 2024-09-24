<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeMeListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'category' =>  $this->category ?? null,
            'title' => $this->title ?? null,
            'short_description' => $this->short_description ?? null,
            'description' => $this->description ?? null,
            'delivery_options' => $this->delivery_options_array ?? [],
            'default_duration' => $this->default_duration ?? null,
            'payments_options' => $this->payment_options_array ?? [],
            'update_prices' => $this->update_prices ?? false,
            'relist' => $this->relist ?? false,
            'user' => $this->user->name ?? null,
            'listed' => $this->update_date?->format('d/m/Y H:i'),
            'relisted' => $this->relist_date?->format('d/m/Y H:i'),
            'photos' => $this->tradeMePhotos->count() ? $this->tradeMePhotos : [],
        ];
    }
}
