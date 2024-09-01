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
            'buying_price' => $baseCard?->price_jp_wholesale,
            'selling_price' => null,
        ]);

        //update pricing
        $rates = CurrencyRate::all();
        $nzd = $rates->where('currency_code', 'NZD')->first();
        $mnt = $rates->where('currency_code', 'MNT')->first();
        $rub = $rates->where('currency_code', 'RUB')->first();

        $pricing_nz_retail = (int) ceil(($baseCard?->price_nz_retail * $nzd->rate_to_jpy) / 100) * 100;
        $pricing_nz_wholesale = (int) ceil(($baseCard?->price_nz_wholesale * $nzd->rate_to_jpy) / 100) * 100;
        $pricing_ru_retail = (int) ceil(($baseCard?->price_ru_retail * $rub->rate_to_jpy) / 100) * 100;
        $pricing_ru_wholesale = (int) ceil(($baseCard?->price_ru_wholesale * $rub->rate_to_jpy) / 100) * 100;
        $pricing_mng_retail = (int) ceil(($baseCard?->price_mng_retail * $mnt->rate_to_jpy) / 100) * 100;
        $pricing_mng_wholesale = (int) ceil(($baseCard?->price_mng_retail * $mnt->rate_to_jpy) / 100) * 100;
        $pricing_jp_retail = $baseCard?->price_jp_retail;
        $pricing_jp_wholesale = $baseCard?->price_jp_wholesale;

        $card->priceCard->refresh();

        if (!$card->priceCard->pricing_nz_retail) {
            $card->priceCard()->update(['pricing_nz_retail' => $pricing_nz_retail]);
        }
        if (!$card->priceCard->pricing_nz_wholesale) {
            $card->priceCard()->update(['pricing_nz_wholesale' => $pricing_nz_wholesale]);
        }
        if (!$card->priceCard->pricing_ru_retail) {
            $card->priceCard()->update(['pricing_ru_retail' => $pricing_ru_retail]);
        }
        if (!$card->priceCard->pricing_ru_wholesale) {
            $card->priceCard()->update(['pricing_ru_wholesale' => $pricing_ru_wholesale]);
        }
        if (!$card->priceCard->pricing_mng_retail) {
            $card->priceCard()->update(['pricing_mng_retail' => $pricing_mng_retail]);
        }
        if (!$card->priceCard->pricing_mng_wholesale) {
            $card->priceCard()->update(['pricing_mng_wholesale' => $pricing_mng_wholesale]);
        }
        if (!$card->priceCard->pricing_jp_retail) {
            $card->priceCard()->update(['pricing_jp_retail' => $pricing_jp_retail]);
        }
        if (!$card->priceCard->pricing_jp_wholesale) {
            $card->priceCard()->update(['pricing_jp_wholesale' => $pricing_jp_wholesale]);
        }

        $card->priceCard->refresh();
        $card->refresh();
        return [
            'price_card' => $card->priceCard,
            'card' => $card,
            'original_card' => $baseCard,
        ];
    }
}
