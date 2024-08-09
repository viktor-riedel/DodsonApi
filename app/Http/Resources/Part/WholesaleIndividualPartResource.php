<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WholesaleIndividualPartResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $country = $request->get('country');

        $buyingPrice = 0;
        if ($this->card->priceCard->buying_price) {
            $buyingPrice = $this->card->priceCard->buying_price;
        }

        if (!$buyingPrice) {
            switch ($country) {
                case 'RU':
                    $buyingPrice = $this->card->priceCard->price_ru_wholesale;
                    break;
                case 'NZ':
                    $buyingPrice = $this->card->priceCard->price_nz_wholesale;
                    break;
                case 'MNG':
                    $buyingPrice = $this->card->priceCard->price_mng_wholesale;
                    break;
                case 'JP':
                    $buyingPrice = $this->card->priceCard->price_jp_wholesale;
                    break;
                default:
                    $buyingPrice = $this->card->priceCard->buying_price;
            }
        }

        return [
            'id' => $this->id,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
            'ic_description' => $this->ic_description,
            'engine' => $this->carPdr->car->modifications?->engine_name,
            'make' => $this->carPdr->car->make,
            'model' => $this->carPdr->car->model,
            'year' => $this->carPdr->car->carAttributes->year,
            'mileage' => $this->carPdr->car->carAttributes->mileage,
            'chassis' => $this->carPdr->car->carAttributes->chassis,
            'generation' => $this->carPdr->car->modifications?->generation,
            'price_jpy' => $buyingPrice,
            'car_images' => PartImageResource::collection($this->carPdr->car->images),
            'part_images' => PartImageResource::collection($this->images),
        ];
    }
}
