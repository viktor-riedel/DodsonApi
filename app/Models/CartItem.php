<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'cart_id',
        'user_id',
        'car_id',
        'part_id',
        'with_engine',
        'without_engine',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(CarPdrPosition::class, 'part_id', 'id');
    }
}
