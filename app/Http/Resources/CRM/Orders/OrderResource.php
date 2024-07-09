<?php

namespace App\Http\Resources\CRM\Orders;

use App\Http\Resources\Order\OrderItemResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            ],
            'items' => OrderItemResource::collection($this->items),
            'created' => $this->created_at->format('d/m/Y'),
            'status' => $this->status_ru ?? Order::ORDER_STATUS_STRING[$this->order_status],
            'order_status_id' => $this->order_status,
            'status_en' => $this->status_en,
            'status_ru' => $this->status_ru,
            'mvr_price' => $this->mvr_price,
            'extra_price' => $this->extra_price,
            'package_price' => $this->package_price,
            'mvr_commission' => $this->mvr_commission,
            'order_number' => $this->order_number,
            'items_count' => $this->items->count(),
            'order_number_formatted' => number_format($this->order_total),
            'order_total' => $this->order_total,
            'country_code' => $this->country_code,
            'created_by' => $this->createdBy->name,
            'user' => $this->createdBy->name,
            'disabled' => $this->order_status === 3,
        ];
    }
}
