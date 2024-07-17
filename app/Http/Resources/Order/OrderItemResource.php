<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'status_en' => $this->item_status_en,
            'status_ru' => Order::ORDER_STATUS_STRING[$this->order->order_status],
            'price' => $this->price_jpy,
            'currency' => $this->currency,
        ];
    }
}
