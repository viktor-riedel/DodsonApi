<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartList extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'item_name_eng',
        'item_name_ru',
        'is_folder',
        'is_virtual',
        'icon_name',
        'key',
        'is_used',
    ];

    protected $casts = [
      'is_folder' => 'boolean',
      'is_virtual' => 'boolean',
      'is_used' => 'boolean',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
