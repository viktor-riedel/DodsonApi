<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdrPositionPhoto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomenclature_base_item_pdr_position_id',
        'folder_name',
        'file_name',
        'original_file_name',
        'photo_url',
        'mime',
        'main_photo',
        'is_video',
        'video_url',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
      'created_at' => 'datetime',
      'updated_at' => 'datetime',
      'is_video' => 'boolean',
      'main_photo' => 'boolean',
    ];
}
