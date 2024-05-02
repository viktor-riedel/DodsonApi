<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarFinance extends Model
{
    protected $fillable = [
        'purchase_price',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
