<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WholesalePartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $country = $request->get('country');

        if ($request->user()) {
            $country = $request->user()->country_code;
        }
        if (!$country) {
            $buyingPrice = $this->card->priceCard->buying_price;
        } else {
            $buyingPrice = match ($country) {
                'RU' => $this->card->priceCard->price_ru_wholesale,
                'NZ' => $this->card->priceCard->price_nz_wholesale,
                'MNG' => $this->card->priceCard->price_mng_wholesale,
                'JP' => $this->card->priceCard->price_jp_wholesale,
                default => 0,
            };
        }

        $currency = match ($country) {
            'RU' => '₽',
            'NZ' => 'NZD',
            'MNG' => '₮',
            default => '¥',
        };


        return [
            'id' => $this->id,
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
            'generation' => $this->carPdr?->car?->generation,
            'price_jpy' => $buyingPrice,
            'price_nzd' => $this->card->priceCard->selling_price,
            'stock_number' => $this->card->barcode,
            'mvr_number' => $this->carPdr->car->car_mvr,
            'currency' => $currency,
            'car_images' => PartImageResource::collection($this->carPdr->car->images),
            'part_images' => PartImageResource::collection($this->images),
        ];
    }
}
