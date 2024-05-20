<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'car_id',
        'part_id',
        'with_engine',
        'without_engine',
        'price_with_engine',
        'price_without_engine',
    ];
}
