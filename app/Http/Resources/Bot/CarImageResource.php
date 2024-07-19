<?php

namespace App\Http\Resources\Bot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'url' => $this->url,
        ];
    }
}
