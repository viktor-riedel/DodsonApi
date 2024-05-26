<?php

namespace App\Http\Resources\CRM\Orders;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_code' => $this->country_code,
            'order_number' => $this->order_number,
            'order_status' => Order::ORDER_STATUS_STRING[$this->order_status],
            'total' => $this->order_total,
            'created' => $this->created_at->format('d/m/Y'),
            'invoice' => $this->invoice_url,
            'items_count' => $this->items_count,
            'user' => $this->createdBy->name,
        ];
    }
}
