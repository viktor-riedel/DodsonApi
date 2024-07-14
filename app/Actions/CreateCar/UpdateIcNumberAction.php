<?php

namespace App\Actions\CreateCar;

use App\Models\Car;
use App\Models\CarPdrPositionCard;
use App\Models\CurrencyRate;
use App\Models\NomenclatureBaseItemPdrCard;
use Illuminate\Http\Request;

class UpdateIcNumberAction
{
    public function handle(Request $request, Car $car, CarPdrPositionCard $card): array
    {
        $card->update(['ic_number' => strtoupper(trim($request->input('ic_number')))]);
        $baseCard = NomenclatureBaseItemPdrCard::where('ic_number', strtoupper(trim($request->input('ic_number'))))
            ->where('description', $card->description)
            ->where('name_eng', $card->name_eng)
            ->first();
        $card->update([
            'parent_inner_id' => $baseCard ? $baseCard->inner_id : $card->parent_inner_id,
        ]);

        $card->position()->update([
            'ic_number' => strtoupper(trim($request->input('ic_number')))
        ]);

        $card->priceCard()->update([
            'price_nz_wholesale' => $baseCard?->price_nz_wholesale,
            'price_nz_retail' => $baseCard?->price_nz_retail,
            'price_ru_wholesale' => $baseCard?->price_ru_wholesale,
            'price_ru_retail' => $baseCard?->price_ru_retail,
            'price_jp_minimum_buy' => $baseCard?->price_jp_maximum_buy,
            'price_jp_maximum_buy' => $baseCard?->price_jp_minimum_buy,
            'minimum_threshold_nz_retail' => $baseCard?->minimum_threshold_nz_retail,
            'minimum_threshold_nz_wholesale' => $baseCard?->minimum_threshold_nz_wholesale,
            'minimum_threshold_ru_retail' => $baseCard?->minimum_threshold_ru_retail,
            'minimum_threshold_ru_wholesale' => $baseCard?->minimum_threshold_ru_wholesale,
            'minimum_threshold_jp_retail' => $baseCard?->minimum_threshold_jp_retail,
            'minimum_threshold_jp_wholesale' => $baseCard?->minimum_threshold_jp_wholesale,
            'minimum_threshold_mng_retail' => $baseCard?->minimum_threshold_mng_retail,
            'minimum_threshold_mng_wholesale' => $baseCard?->minimum_threshold_mng_wholesale,
            'delivery_price_nz' => $baseCard?->delivery_price_nz,
            'delivery_price_ru' => $baseCard?->delivery_price_ru,
            'pinnacle_price' => $baseCard?->pinnacle_price,
            'price_currency' => 'JPY',
            'price_mng_wholesale' => $baseCard?->price_mng_wholesale,
            'price_mng_retail' => $baseCard?->price_mng_retail,
            'price_jp_retail' => $baseCard?->price_jp_retail,
            'price_jp_wholesale' => $baseCard?->price_jp_wholesale,
            'nz_team_price' => $baseCard?->nz_team_price,
            'nz_team_needs' => $baseCard?->nz_team_needs,
            'nz_needs' => $baseCard?->nz_needs,
            'ru_needs' => $baseCard?->ru_needs,
            'jp_needs' => $baseCard?->jp_needs,
            'mng_needs' => $baseCard?->mng_needs,
            'needs' => $baseCard?->needs,
        ]);

        //update pricing
        $rates = CurrencyRate::all();
        $nzd = $rates->where('currency_code', 'NZD')->first();
        $mnt = $rates->where('currency_code', 'MNT')->first();
        $rub = $rates->where('currency_code', 'RUB')->first();

        //update pricing
        $card->priceCard()->update([
            'pricing_nz_retail' => (int) round($baseCard?->price_nz_retail * $nzd->rate_to_jpy, 3),
            'pricing_nz_wholesale' => (int) round($baseCard?->price_nz_wholesale * $nzd->rate_to_jpy, 3),
            'pricing_ru_retail' => (int) round($baseCard?->price_ru_retail * $rub->rate_to_jpy, 3),
            'pricing_ru_wholesale' => (int) round($baseCard?->price_ru_wholesale * $rub->rate_to_jpy, 3),
            'pricing_mng_retail' => (int) round($baseCard?->price_mng_retail * $mnt->rate_to_jpy, 3),
            'pricing_mng_wholesale' => (int) round($baseCard?->price_mng_wholesale * $mnt->rate_to_jpy, 3),
            'pricing_jp_retail' => $baseCard?->price_jp_retail,
            'pricing_jp_wholesale' => $baseCard?->price_jp_wholesale,
        ]);


//        $clientCountryCode = $card->position->client?->country_code;
//        $isWholeSeller = $card->position->client?->wholesaler ?? false;

        //update selling and buying prices
        // NOTE DISABLED FOR NOW
//        if ($clientCountryCode && !$card->priceCard->selling_price) {
//            switch ($clientCountryCode) {
//                case 'RU':
//                    $card->priceCard()->update([
//                        'buying_price' => $isWholeSeller ? $baseCard?->price_ru_wholesale : $baseCard?->price_ru_retail,
//                        'selling_price' => $isWholeSeller && $baseCard?->price_ru_wholesale ?
//                            $baseCard?->price_ru_wholesale : $baseCard?->price_ru_retail,
//                    ]);
//                    break;
//                case 'NZ':
//                    if ($card->priceCard->selling_price) {
//                        $card->priceCard()->update([
//                            'buying_price' => $isWholeSeller ? $baseCard?->price_nz_wholesale : $baseCard?->price_nz_retail,
//                            'selling_price' => $isWholeSeller && $baseCard?->price_nz_wholesale ?
//                                $baseCard?->price_nz_wholesale : $baseCard?->price_nz_retail,
//                        ]);
//                    }
//                    break;
//                case 'MN':
//                    if ($card->priceCard->selling_price) {
//                        $card->priceCard()->update([
//                            'buying_price' => $isWholeSeller ? $baseCard?->price_mng_wholesale : $baseCard?->price_mng_retail,
//                            'selling_price' => $isWholeSeller && $baseCard?->price_mng_wholesale ?
//                                $baseCard?->price_mng_wholesale : $baseCard?->price_mng_retail,
//                        ]);
//                    }
//                    break;
//                default:
//                    if ($card->priceCard->selling_price) {
//                        $card->priceCard()->update([
//                            'buying_price' => $isWholeSeller ? $baseCard?->price_jp_wholesale : $baseCard?->price_jp_retail,
//                            'selling_price' => $isWholeSeller && $baseCard?->price_jp_wholesale ?
//                                $baseCard?->price_jp_wholesale : $baseCard?->price_jp_retail,
//                        ]);
//                    }
//                    break;
//            }
//        }
        $card->priceCard->refresh();
        $card->refresh();
        return [
            'price_card' => $card->priceCard,
            'card' => $card,
            'original_card' => $baseCard,
        ];
    }
}
