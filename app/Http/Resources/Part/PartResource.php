<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_number' => $this->stock_number,
            'ic_number' => $this->ic_number,
            'ic_description' => $this->ic_description,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'item_name_jp' => $this->item_name_jp,
            'item_name_mng' => $this->item_name_mng,
            'price_jpy' => $this->price_jpy,
            'images'=> [],
            'modification' => null,
        ];
    }
}
