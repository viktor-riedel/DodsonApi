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
        $total = $this->items->sum('price_jpy');
        return [
            'id' => $this->id,
            'car' => [
                'make' => $this->items->first()?->car?->make ?? 'Parts Order',
                'model' => $this->items->first()?->car?->model ?? '-',
                'year' => $this->items->first()?->car?->carAttributes?->year ?? '-',
                'chassis' => $this->items->first()?->car?->chassis ?? '-',
                'car_mvr' => $this->items->first()?->car?->car_mvr ?? '-',
                'photo' => $this->items->first()?->car?->images->first()?->url,
            ],
            'is_parts_order' => !$this->items->first()?->car,
            'items' => OrderItemResource::collection($this->items),
            'created' => $this->created_at->format('d/m/Y'),
            'status' => Order::ORDER_STATUS_STRING[$this->order_status],
            'order_status_id' => $this->order_status,
            'status_en' => $this->status_en,
            'status_ru' => $this->status_ru,
            'order_number' => $this->sync_order_number ?? $this->order_number,
            'items_count' => $this->items->count(),
            'order_number_formatted' => number_format($total),
            'order_total' => $total,
            'country_code' => $this->country_code,
            'created_by' => $this->createdBy->name,
            'user' => $this->createdBy->name,
            'disabled' => $this->order_status === 3,
            'sync' => $this->latestSync?->document_number,
        ];
    }
}
