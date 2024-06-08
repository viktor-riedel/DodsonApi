<?php

namespace App\Http\Resources\CRM\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_description' => $this->ic_description,
            'ic_number' => $this->ic_number,
            'images' => ImageResource::collection($this->card->images),
            'attributes' => [
                'color' => $this->card->partAttributesCard->color,
                'volume' => $this->card->partAttributesCard->volume,
                'weight' => $this->card->partAttributesCard->weight,
            ],
            'prices' => [
                'buying_price' => $this->card->priceCard->buying_price,
                'delivery_price_nz' => $this->card->priceCard->delivery_price_nz,
                'delivery_price_ru' => $this->card->priceCard->delivery_price_ru,
                'jp_needs' => $this->card->priceCard->jp_needs,
                'minimum_threshold_jp_retail' => $this->card->priceCard->minimum_threshold_jp_retail,
                'minimum_threshold_jp_wholesale' => $this->card->priceCard->minimum_threshold_jp_wholesale,
                'minimum_threshold_mng_retail' => $this->card->priceCard->minimum_threshold_mng_retail,
                'minimum_threshold_mng_wholesale' => $this->card->priceCard->minimum_threshold_mng_wholesale,
                'minimum_threshold_nz_retail' => $this->card->priceCard->minimum_threshold_nz_retail,
                'minimum_threshold_nz_wholesale' => $this->card->priceCard->minimum_threshold_nz_wholesale,
                'minimum_threshold_ru_retail' => $this->card->priceCard->minimum_threshold_ru_retail,
                'minimum_threshold_ru_wholesale' => $this->card->priceCard->minimum_threshold_ru_wholesale,
                'mng_needs' => $this->card->priceCard->mng_needs,
                'needs' => $this->card->priceCard->needs,
                'nz_needs' => $this->card->priceCard->nz_needs,
                'nz_team_needs' => $this->card->priceCard->nz_team_needs,
                'nz_team_price' => $this->card->priceCard->nz_team_price,
                'pinnacle_price' => $this->card->priceCard->pinnacle_price,
                'price_currency' => $this->card->priceCard->price_currency,
                'price_jp_maximum_buy' => $this->card->priceCard->price_jp_maximum_buy,
                'price_jp_minimum_buy' => $this->card->priceCard->price_jp_minimum_buy,
                'price_jp_retail' => $this->card->priceCard->price_jp_retail,
                'price_jp_wholesale' => $this->card->priceCard->price_jp_wholesale,
                'price_mng_retail' => $this->card->priceCard->price_mng_retail,
                'price_mng_wholesale' => $this->card->priceCard->price_mng_wholesale,
                'price_nz_retail' => $this->card->priceCard->price_nz_retail,
                'price_nz_wholesale' => $this->card->priceCard->price_nz_wholesale,
                'price_ru_retail' => $this->card->priceCard->price_ru_retail,
                'price_ru_wholesale' => $this->card->priceCard->price_ru_wholesale,
                'selling_price' => $this->card->priceCard->selling_price,
                'ru_needs' => $this->card->priceCard->ru_needs,
            ],
        ];
    }
}
