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
            'mvr_number' => $this->car_mvr,
            'stock_number' => $this->barcode,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => Str::replace('(NZ ONLY)', '', $this->description),
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'item_name_eng' => $this->name_eng,
            'item_name_ru' => $this->name_ru,
            'item_name_jp' => '',
            'item_name_mng' => '',
            'price_nzd' => $this->selling_price,
            'images'=> $this->images->count() ?
                PartPhotoResource::collection($this->images) :
                [
                    [
                        'id' => 0,
                        'url' => '/public/part_not_found.jpg'
                    ],
                ],
            'group_name' => $this->item_name_eng,
            'trademe' => $this->tradeMeListing !== null,
            'generation' =>  $this->generation,
            'modification' => null,
            'image' => $this->images->count() ? $this->images->first()->url : '/public/part_not_found.jpg',
            'user' => $this->buyer,
            'order' => $this->order?->order_number,
            'order_id' => $this->order?->id,
        ];
    }
}
