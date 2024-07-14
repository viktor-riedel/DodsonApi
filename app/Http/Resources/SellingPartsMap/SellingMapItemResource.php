<?php

namespace App\Http\Resources\SellingPartsMap;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellingMapItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_folder' => $this->parent_id === 0,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'price_jp' => $this->price_jp,
            'price_ru' => $this->price_ru,
            'price_nz' => $this->price_nz,
            'price_mng' => $this->price_mng,
            'items' => self::collection($this->items),
            'available' => $this->available,
        ];
    }
}
