<?php

namespace App\Http\Resources\StockCars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'year' => $this->carAttributes->year &&  $this->carAttributes->year > 0 ? $this->carAttributes->year : '',
            'chassis' => $this->carAttributes->chassis,
            'color' => $this->carAttributes->color,
            'engine' => $this->carAttributes->engine,
            'mileage' => number_format($this->carAttributes->mileage),
            'price' => [
                'price_with_engine' => number_format($this->carFinance?->price_with_engine),
                'price_without_engine' => number_format($this->carFinance?->price_without_engine),
            ],
            'images' => $this->whenLoaded('images', PhotoResource::collection($this->images), []),
            'modifications' => $this->whenLoaded('modifications',
                    new ModificationResource($this->modifications),
                null),
        ];
    }
}
