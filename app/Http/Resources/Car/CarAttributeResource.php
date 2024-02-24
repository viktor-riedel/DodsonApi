<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chassis' => $this->chassis,
            'year' => $this->year,
            'color' => $this->color,
            'engine' => $this->engine,
            'mileage' => $this->mileage,
        ];
    }
}
