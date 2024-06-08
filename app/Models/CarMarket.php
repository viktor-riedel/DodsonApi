<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarMarket extends Model
{
    protected $fillable = [
      'car_id',
      'country_code'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
