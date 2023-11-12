<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'yard_name' => $this->yard_name,
            'location_country' => $this->location_country,
            'address' => $this->address,
            'approx_shipping_days' => $this->approx_shipping_days,
        ];
    }
}
