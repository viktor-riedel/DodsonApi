<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'yard_name',
        'location_country',
        'address',
        'approx_shipping_days',
    ];
}
