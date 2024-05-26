<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPositionCardPrice extends Model
{
    use SoftDeletes;

    public const PRICE_CURRENCIES = [
       'JPY' => '¥',
       'USD' => '$',
       'NZD' => 'NZD',
       'RUB' => '₽',
       'MNT' => '₮'
    ];

    protected $fillable = [
        'car_pdr_position_card_id',
        'price_nz_wholesale',
        'price_nz_retail',
        'price_ru_wholesale',
        'price_ru_retail',
        'price_jp_minimum_buy',
        'price_jp_maximum_buy',
        'minimum_threshold_nz_retail',
        'minimum_threshold_nz_wholesale',
        'minimum_threshold_ru_retail',
        'minimum_threshold_ru_wholesale',
        'minimum_threshold_jp_retail',
        'minimum_threshold_jp_wholesale',
        'minimum_threshold_mng_retail',
        'minimum_threshold_mng_wholesale',
        'delivery_price_nz',
        'delivery_price_ru',
        'pinnacle_price',
        'price_currency',
        'approximate_price',
        'real_price',
        'price_mng_wholesale',
        'price_mng_retail',
        'price_jp_retail',
        'price_jp_wholesale',
        'nz_team_price',
        'nz_team_needs',
        'nz_needs',
        'ru_needs',
        'jp_needs',
        'mng_needs',
        'needs',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public static function getCurrenciesJson(): array
    {
        $currencies = [];
        foreach (self::PRICE_CURRENCIES as $currency => $symbol) {
            $currencies[] = [
                'code' => $currency,
                'symbol' => $symbol,
            ];
        }

        return $currencies;
    }
}
