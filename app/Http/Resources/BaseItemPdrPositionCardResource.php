<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemPdrPositionCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomenclature_base_item_pdr_position_id' => $this->nomenclature_base_item_pdr_position_id,
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
            'price_mng_retail' => $this->price_mng_retail,
            'price_mng_wholesale' => $this->price_mng_wholesale,
            'minimum_threshold_mng_retail' => $this->minimum_threshold_mng_retail,
            'minimum_threshold_mng_wholesale' => $this->minimum_threshold_mng_wholesale,
            'minimum_threshold_jp_retail' => $this->minimum_threshold_jp_retail,
            'minimum_threshold_jp_wholesale' => $this->minimum_threshold_jp_wholesale,
            'price_jp_wholesale' => $this->price_jp_wholesale,
            'price_jp_retail' => $this->price_jp_retail,
            'nz_needs' => $this->nz_needs,
            'ru_needs' => $this->ru_needs,
            'mng_needs' => $this->mng_needs,
            'jp_needs' => $this->jp_needs,
            'needs' => $this->needs,
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
