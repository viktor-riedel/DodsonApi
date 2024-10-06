<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartNameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'item_name_eng' => $this->item_name_eng,
        ];
    }
}
