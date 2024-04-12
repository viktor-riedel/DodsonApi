<?php

namespace App\Http\Resources\AvailableCars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'model' => $this->resource,
        ];
    }
}
