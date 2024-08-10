<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WholesalePartsAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mvr' => $this->carPdr?->car?->car_mvr,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
            'ic_description' => $this->ic_description,
            'engine' => $this->carPdr?->car?->modifications?->engine_name,
            'make' => $this->carPdr?->car?->make,
            'model' => $this->carPdr?->car?->model,
            'year' => $this->carPdr?->car?->carAttributes?->year,
            'mileage' => $this->carPdr?->car?->carAttributes?->mileage,
            'chassis' => $this->carPdr?->car?->carAttributes?->chassis,
            'generation' => $this->carPdr?->car?->modifications?->generation,
            'price_nz_wholesale' => $this->card->priceCard->pricing_nz_wholesale,
            'price_mng_wholesale' => $this->card->priceCard->pricing_mng_wholesale,
            'price_ru_wholesale' => $this->card->priceCard->pricing_ru_wholesale,
            'buying_price' => $this->card->priceCard->selling_price,
            'selling_price' => $this->card->priceCard->buying_price,
            'client' => $this->client ? $this->client->name : null,
            'for_sale' =>  $this->carPdr?->car?->carFinance?->parts_for_sale ?? false,
            'markets' => $this->carPdr?->car?->markets ?
                    implode(',', $this->carPdr?->car?->markets?->pluck('country_code')->toArray()) :
                    '',
        ];
    }
}
