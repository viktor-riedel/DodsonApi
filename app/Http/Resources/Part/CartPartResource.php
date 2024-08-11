<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartPartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
        ];
    }
}
