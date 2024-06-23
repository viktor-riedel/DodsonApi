<?php

namespace App\Http\Resources\SellingPartsMap;

use App\Models\OrderItem;
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
            'price_jpy' => $this->price_jpy,
            'price_rub' => $this->price_rub,
            'price_nzd' => $this->price_nzd,
            'price_mng' => $this->price_mng,
            'items' => self::collection($this->items),
            'available' => false,
        ];
    }
}
