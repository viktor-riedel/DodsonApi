<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class EditPartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_number' => $this->card->barcode,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => Str::replace('(NZ ONLY)', '', $this->ic_description),
            'make' => $this->carPdr->car->make,
            'model' => $this->carPdr->car->model,
            'year' => $this->carPdr->car->carAttributes->year,
            'mileage' => $this->carPdr->car->carAttributes->mileage,
            'generation' => $this->carPdr->car->generation,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'item_name_jp' => '',
            'item_name_mng' => '',
            'standard_price_nzd' => $this->card->priceCard->standard_price,
            'price_nzd' => $this->card->priceCard->selling_price,
            'images'=> [], //PartPhotoResource::collection($this->images),
            'group_name' => $this->carPdr->item_name_eng,
            'trademe' => false,
            'modification' => null,
            'image' => $this->images->count() ? $this->images->first()->url : '/public/part_not_found.jpg',
        ];
    }
}
