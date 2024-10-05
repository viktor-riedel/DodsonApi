<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMeListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_pdr_position_id',
        'listing_id',
        'listed_by',
        'title',
        'category',
        'category_name',
        'short_description',
        'description',
        'delivery_options',
        'default_duration',
        'payments_options',
        'update_prices',
        'relist',
        'relist_date',
        'update_date',
    ];

    protected $casts = [
        'update_prices' => 'boolean',
        'relist' => 'boolean',
        'relist_date' => 'datetime',
        'update_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'listed_by');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(CarPdrPosition::class, 'car_pdr_position_id');
    }

    public function getDeliveryOptionsArrayAttribute(): array
    {
        if ($this->delivery_options) {
            return explode(',', $this->delivery_options);
        }

        return [];
    }

    public function getPaymentOptionsArrayAttribute(): array
    {
        if ($this->payments_options) {
            return explode(',', $this->payments_options);
        }

        return [];
    }

    public function tradeMePhotos(): HasMany
    {
        return $this->hasMany(TradeMeListingPhotos::class);
    }
}
