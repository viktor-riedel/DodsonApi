<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $thos->phone,
            'address' => $this->address,
            'country' => $this->country,
            'is_wholeseller' => $this->is_wholeseller,
        ];
    }
}
