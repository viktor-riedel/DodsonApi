<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarFinance extends Model
{
    protected $fillable = [
        'purchase_price',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
