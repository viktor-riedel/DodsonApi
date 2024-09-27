<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class PartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_number' => $this->original_barcode,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => Str::replace('(NZ ONLY)', '', $this->ic_description),
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'item_name_jp' => $this->item_name_jp,
            'item_name_mng' => $this->item_name_mng,
            'price_nzd' => $this->actual_price_nzd,
            'images'=> PartPhotoResource::collection($this->images),
            'group_name' => $this->part_group,
            'trademe' => $this->tradeMeListing !== null,
            'generation' =>  $this->generation,
            'modification' => null,
            'image' => $this->images->count() ? $this->images->first()->url : null,
        ];
    }
}
