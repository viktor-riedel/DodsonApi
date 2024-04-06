<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarAttribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_id',
        'chassis',
        'year',
        'color',
        'engine',
        'mileage',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];
}
