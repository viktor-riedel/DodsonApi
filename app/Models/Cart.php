<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class)
            ->where('cart_id', $this->id);
    }

    public function carItems(): HasMany
    {
        return $this->HasMany(CartItem::class)
            ->where('cart_id', $this->id)
            ->whereNotNull('car_id');
    }

    public function partItems(): HasMany
    {
        return $this->HasMany(CartItem::class)
            ->where('cart_id', $this->id)
            ->whereNotNull('part_id');
    }
}
