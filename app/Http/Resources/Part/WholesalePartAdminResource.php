<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WholesalePartAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
            'ic_description' => $this->ic_description,
            'car' => [
                'id' => $this->carPdr->car->id,
                'make' => $this->carPdr->car->make,
                'model' => $this->carPdr->car->model,
                'year' => $this->carPdr->car->carAttributes->year,
                'chassis' => $this->carPdr->car->carAttributes->chassis,
                'color' => $this->carPdr->car->carAttributes->color,
                'mileage' => $this->carPdr->car->carAttributes->mileage,
                'markets' => $this->carPdr->car->markets,
                'parts_for_sale' => $this->carPdr->car->carFinance->parts_for_sale,
                'modification' => [
                    'inner_id' => $this->carPdr->car->modifications->inner_id,
                    'header' => $this->carPdr->car->modifications->header,
                    'generation' => $this->carPdr->car->modifications->generation,
                    'engine_name' => $this->carPdr->car->modifications->engine_name,
                    'engine_type' => $this->carPdr->car->modifications->engine_type,
                    'engine_size' => $this->carPdr->car->modifications->engine_size,
                    'engine_power' => $this->carPdr->car->modifications->engine_power,
                    'doors' => $this->carPdr->car->modifications->doors,
                    'transmission' => $this->carPdr->car->modifications->transmission,
                    'drive_train' => $this->carPdr->car->modifications->drive_train,
                    'chassis' => $this->carPdr->car->modifications->chassis,
                    'body_type' => $this->carPdr->car->modifications->body_type,
                ],
            ],
            'prices' => [
                'price_nz_retail' => $this->card->priceCard->price_nz_retail,
                'price_nz_wholesale' => $this->card->priceCard->price_nz_wholesale,
                'price_ru_retail' => $this->card->priceCard->price_ru_retail,
                'price_ru_wholesale' => $this->card->priceCard->price_ru_wholesale,
                'price_mng_retail' => $this->card->priceCard->price_mng_retail,
                'price_mng_wholesale' => $this->card->priceCard->price_mng_wholesale,
                'price_jp_retail' => $this->card->priceCard->price_jp_retail,
                'price_jp_wholesale' => $this->card->priceCard->price_jp_wholesale,
                'buying_price' => $this->card->priceCard->buying_price,
                'selling_price' => $this->card->priceCard->selling_price,
                'original_card' => $this->original_card,
            ],
            'photos' => $this->images ? PartImageResource::collection($this->images) : [],
        ];
    }
}
