<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemPdrPositionCardApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name_eng' => $this->name_eng,
            'name_ru' => $this->name_ru,
            'price_nz_wholesale' => $this->price_nz_wholesale,
            'price_nz_retail' => $this->price_nz_retail,
            'price_ru_wholesale' => $this->price_ru_wholesale,
            'price_ru_retail' => $this->price_ru_retail,
            'price_jp_minimum_buy' => $this->price_jp_minimum_buy,
            'price_jp_maximum_buy' => $this->price_jp_maximum_buy,
            'minimum_threshold_nz_retail' => $this->minimum_threshold_nz_retail,
            'minimum_threshold_nz_wholesale' => $this->minimum_threshold_nz_wholesale,
            'minimum_threshold_ru_retail' => $this->minimum_threshold_ru_retail,
            'minimum_threshold_ru_wholesale' => $this->minimum_threshold_ru_wholesale,
            'delivery_price_nz' => $this->delivery_price_nz,
            'delivery_price_ru' => $this->delivery_price_ru,
            'pinnacle_price' => $this->pinnacle_price,
            'volume' => $this->volume,
            'trademe' => $this->trademe,
            'drom' => $this->drom,
            'avito' => $this->avito,
            'dodson' => $this->dodson,
            'nova' => $this->nova,
            'comment' => $this->comment,
            'description' => $this->description,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'color' => $this->color,
            'weight' => $this->weight,
        ];
    }
}
