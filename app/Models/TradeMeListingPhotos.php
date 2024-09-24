<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMeListingPhotos extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trade_me_listing_id',
        'image_url',
    ];

    public function tradeMeListing(): BelongsTo
    {
        return $this->belongsTo(TradeMeListing::class);
    }
}
