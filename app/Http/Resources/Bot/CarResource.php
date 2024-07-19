<?php

namespace App\Http\Resources\Bot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'chassis' => $this->chassis,
            'car_mvr' => $this->car_mvr,
            'year' => $this->carAttributes->year,
            'mileage' => $this->carAttributes->mileage,
            'markets' => CarMarketResource::collection($this->markets),
            'images' => CarImageResource::collection($this->images),
            'modification' => new CarModificationResource($this->modifications),
        ];
    }
}
