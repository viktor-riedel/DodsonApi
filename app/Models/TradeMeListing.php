<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMeListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'part_id',
        'listing_id',
        'listed_by',
        'title',
        'category',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'listed_by');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}
