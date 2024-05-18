<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_car' => $this->car_id !== null,
            'is_part' => $this->part_id !== null,
            'car_id' => $this->car_id,
            'part_id' => $this->part_id,
        ];
    }
}
