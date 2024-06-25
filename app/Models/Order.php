<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    public const ORDER_STATUS_STRING = [
        0 => 'PENDING',
        1 => 'CONFIRMED',
        2 => 'PROCESSING',
        3 => 'COMPLETE',
    ];

    public const ORDER_STATUS_INT = [
        'PENDING' => 0,
        'CONFIRMED' => 1,
        'PROCESSING' => 2,
        'COMPLETE' => 3,
    ];

    public const ORDER_START_NUMBER = 1000;

    protected $fillable = [
        'user_id',
        'order_number',
        'order_status',
        'invoice_url',
        'order_total',
        'country_code',
        'comment',
        'reference',
        'status_en',
        'status_ru',
        'total_amount',
        'mvr_price',
        'extra_price',
        'package_price',
        'mvr_commission',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function getNextOrderNumber(): int
    {
        $ordersCount = self::withTrashed()->get()->count();
        return self::ORDER_START_NUMBER + ($ordersCount + 1);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function carItems(): HasMany
    {
        return $this->items()->whereNotNull('car_id');
    }

    public function partsItems(): HasMany
    {
        return $this->items()->whereNotNull('part_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
