<?php

namespace App\Http\Resources\WishList;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishListCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
