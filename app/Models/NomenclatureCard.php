<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_eng',
        'name_ru',
        'default_price',
        'default_wholesale_price',
        'default_retail_price',
        'default_special_price',
        'wholesale_rus_price',
        'wholesale_nz_price',
        'retail_rus_price',
        'retail_nz_price',
        'special_rus_price',
        'special_nz_price',
        'comment',
        'description',
        'status',
        'condition',
        'tag',
        'yard',
        'bin',
        'is_new',
        'is_scrap',
        'ic_number',
        'oem_number',
        'inner_number',
        'color',
        'weight',
        'extra',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_new' => 'boolean',
        'is_scrap' => 'boolean',
    ];
}
