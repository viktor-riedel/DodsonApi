<?php

namespace App\Actions\UpdatePrices;

use App\Models\NomenclatureBaseItemPdrCard;

class UpdatePricesAction
{
    public function handle(array $data): array
    {
        $result = [
            'success' => [],
            'error' => [],
        ];
        $date = $data['Date'] ?? '';
        $docNumber = $data['DocNumber'] ?? '';
        $baseCar = $data['BaseCarName'] ?? '';
        if (isset($data['Needs']) && is_array($data['Needs'])) {
            $result = true;
            foreach($data['Needs'] as $need) {
                $card = NomenclatureBaseItemPdrCard::where('inner_id', $need['id'])->first();
                if (!$card) {
                    $result['error'][] = $need['id'];
                } else {
                    $card->update([
                        'price_nz_wholesale' => isset($need['price_nz_wholesale']) ? (int) $need['price_nz_wholesale'] : null,
                        'price_nz_retail' => isset($need['price_nz_retail']) ? (int) $need['price_nz_retail'] : null,
                        'price_ru_wholesale' => isset($need['price_ru_wholesale']) ? (int) $need['price_ru_wholesale'] : null,
                        'price_jp_retail' => isset($need['price_jp_wholesale']) ? (int) $need['price_jp_wholesale'] : null,
                        'price_mng_retail' => isset($need['price_mng_retail']) ? (int) $need['price_mng_retail'] : null,
                        'price_mng_wholesale' => isset($need['price_mng_wholesale']) ? (int) $need['price_mng_wholesale'] : null,
                        'minimum_threshold_jp_wholesale' => isset($need['price_jp_wholesale']) ? (int) $need['price_jp_wholesale'] : null,
                        'minimum_threshold_nz_retail' => isset($need['MinimumLeftNZRetail']) ? (int) $need['MinimumLeftNZRetail'] : null,
                        'minimum_threshold_nz_wholesale' => isset($need['MinimumLeftNZWhosale']) ? (int) $need['MinimumLeftNZWhosale'] : null,
                        'minimum_threshold_ru_retail' => isset($need['MinimumLeftRuRetail']) ? (int) $need['MinimumLeftRuRetail'] : null,
                        'minimum_threshold_ru_wholesale' => isset($need['MinimumLeftRuWhosale']) ? (int) $need['MinimumLeftRuWhosale'] : null,
                        'minimum_threshold_mng_retail' => isset($need['MinimumLeftMNGRetail']) ? (int) $need['MinimumLeftMNGRetail'] : null,
                        'minimum_threshold_mng_wholesale' => isset($need['MinimumLeftMNGWhosale']) ? (int) $need['MinimumLeftMNGWhosale'] : null,
                        'nz_needs' => isset($need['NZNeed']) ? (int) $need['NZNeed'] : null,
                        'ru_needs' => isset($need['RuNeed']) ? (int) $need['RuNeed'] : null,
                        'mng_needs' => isset($need['MngNeed']) ? (int) $need['MngNeed'] : null,
                        'jp_needs' => isset($need['JpNeed']) ? (int) $need['JpNeed'] : null,
                    ]);
                    $result['success'][] = $need['id'];
                }
            }
        }
        return $result;
    }
}
