<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car' => [
                'make' => $this->items->first()?->car?->make,
                'model' => $this->items->first()?->car?->model,
                'year' => $this->items->first()?->car?->carAttributes?->year,
                'chassis' => $this->items->first()?->car?->chassis,
                'car_mvr' => $this->items->first()?->car?->car_mvr,
                'modification' => $this->items->first()?->car?->modifications,
                'photos' => $this->items->first()?->car?->images()->pluck('url')->toArray(),
            ],
            'is_parts_order' => $this->items->first()?->car === null,
            'items' => $this->items,
            'created' => $this->created_at->format('d/m/Y'),
            'status' => Order::ORDER_STATUS_STRING[$this->order_status],
            'total_amount' => $this->total_amount,
            'order_number' => $this->order_number,
            'order_total_formatted' => number_format($this->items->sum('price_jpy')),
            'order_total' => $this->items->sum('price_jpy'),
        ];
    }
}
