<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currency = '¥';
        if ($this->country_code === 'RU') {
            $currency = '₽';
        } else if ($this->country_code === 'MN') {
            $currency = '';
        } else if ($this->country_code === 'NZ') {
            $currency = '$';
        }

        return [
            'id' => $this->id,
            'items' => $this->items,
            'created' => $this->created_at->format('d/m/Y'),
            'status' => Order::ORDER_STATUS_STRING[$this->order_status],
            'order_number' => $this->order_number,
            'order_number_formatted' => number_format($this->order_total),
            'order_total' => $this->order_total,
            'currency' => $currency,
        ];
    }
}
