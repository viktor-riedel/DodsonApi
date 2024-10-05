<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetailPartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => $this->ic_description,
            'item_name_eng' => $this->item_name_eng,
            'group' => $this->carPdr->item_name_eng,
            'stock_number' => $this->card->barcode,
            'mvr_number' => $this->carPdr->car->car_mvr,
            'make' => $this->carPdr->car->make,
            'model' => $this->carPdr->car->model,
            'year' => $this->carPdr->car->carAttributes->year,
            'generation' => $this->carPdr->car->generation,
            'mileage' => number_format($this->carPdr->car->carAttributes->mileage),
            'price_nzd' => $this->card->priceCard->selling_price,
            'image' => $this->images->count() ? $this->images->first()->url : '/public/part_not_found.jpg',
        ];
    }
}
