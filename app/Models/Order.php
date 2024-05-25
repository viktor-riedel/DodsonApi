<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function getNextOrderNumber(): int
    {
        $ordersCount = self::all()->count();
        return self::ORDER_START_NUMBER + ($ordersCount + 1);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
