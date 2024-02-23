<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPosition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_pdr_id',
        'item_name_ru',
        'item_name_eng',
        'ic_number',
        'oem_number',
        'ic_description',
        'is_virtual',
        'created_by',
        'deleted_by',
    ];
}
