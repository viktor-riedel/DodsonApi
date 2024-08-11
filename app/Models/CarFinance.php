<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarFinance extends Model
{
    protected $fillable = [
        'purchase_price',
        'car_is_for_sale',
        'price_with_engine_nz',
        'price_without_engine_nz',
        'price_without_engine_ru',
        'price_with_engine_ru',
        'price_with_engine_mn',
        'price_without_engine_mn',
        'price_with_engine_jp',
        'price_without_engine_jp',
        'car_is_for_sale',
        'parts_for_sale',
    ];

    protected $casts = [
        'car_is_for_sale' => 'boolean',
        'parts_for_sale' => 'boolean',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
