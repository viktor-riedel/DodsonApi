<?php

namespace App\Http\Resources\StockCars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenerationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'generation' => $this->generation,
        ];
    }
}
