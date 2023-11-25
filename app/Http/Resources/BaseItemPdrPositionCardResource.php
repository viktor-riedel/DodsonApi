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
            'default_price' => $this->default_price,
            'default_retail_price' => $this->default_retail_price,
            'default_wholesale_price' => $this->default_wholesale_price,
            'default_special_price' => $this->default_special_price,
            'wholesale_rus_price' => $this->wholesale_rus_price,
            'wholesale_nz_price' => $this->wholesale_nz_price,
            'retail_rus_price' => $this->retail_rus_price,
            'retail_nz_price' => $this->retail_nz_price,
            'special_rus_price' => $this->special_rus_price,
            'special_nz_price' => $this->special_nz_price,
            'comment' => $this->comment,
            'description' => $this->description,
            'status' => $this->status,
            'condition' => $this->condition,
            'tag' => $this->tag,
            'yard' => $this->yard,
            'bin' => $this->bin,
            'is_new' => $this->is_new,
            'is_scrap' => $this->is_scrap,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'inner_number' => $this->inner_number,
            'color' => $this->color,
            'weight' => $this->weight,
            'extra' => $this->extra,
        ];
    }
}
