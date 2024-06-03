<?php

namespace App\Http\Resources\StockCars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'year' => $this->year,
        ];
    }
}
