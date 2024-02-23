<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPositionCardAttribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_pdr_position_card_id',
        'color',
        'weight',
        'volume',
        'trademe',
        'drom',
        'avito',
        'dodson',
    ];
}
