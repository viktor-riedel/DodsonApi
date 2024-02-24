<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->mediable_id,
            'url' => $this->url,
        ];
    }
}
