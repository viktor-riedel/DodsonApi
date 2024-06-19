<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'car_id',
        'part_id',
        'with_engine',
        'item_name_eng',
        'item_name_ru',
        'price_jpy',
        'engine_price',
        'catalyst_price',
        'comment',
        'user_id',
        'item_status_en',
        'item_status_ru',
        'currency',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
