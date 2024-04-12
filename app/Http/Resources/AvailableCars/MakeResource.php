<?php

namespace App\Http\Resources\AvailableCars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MakeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'make' => $this->resource,
        ];
    }
}
