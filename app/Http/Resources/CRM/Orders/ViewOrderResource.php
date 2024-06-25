<?php

namespace App\Http\Resources\CRM\Orders;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_code' => $this->country_code,
            'created_by' => $this->createdBy->name,
            'order_status' => Order::ORDER_STATUS_STRING[$this->order_status],
            'order_status_id' => $this->order->order_status,
            'created' => $this->order->created_at->format('d/m/Y'),
            'order_number' => $this->order_number,
            'invoice' => $this->invoice_url,
            'order_total' => $this->order_total,
            'cars' => ViewOrderCarResource::collection($this->cars),
        ];
    }
}
