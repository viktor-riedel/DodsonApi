<?php

namespace App\Http\Resources\Bot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarMarketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'country' => findCountryByCode($this->country_code),
        ];
    }
}
