<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPositionCardPrice extends Model
{
    use SoftDeletes;

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
        'delivery_price_nz',
        'delivery_price_ru',
        'pinnacle_price',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];
}
