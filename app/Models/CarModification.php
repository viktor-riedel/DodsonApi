<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarModification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_id',
        'body_type',
        'chassis',
        'generation',
        'gen_number',
        'doors',
        'engine_size',
        'drive_train',
        'header',
        'month_from',
        'month_to',
        'restyle',
        'transmission',
        'year_from',
        'year_to',
        'years_string',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];
}
