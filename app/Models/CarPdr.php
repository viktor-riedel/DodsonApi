<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdr extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_id',
        'car_pdr_card_id',
        'car_pdr_position_id',
        'parent_id',
        'item_name_eng',
        'item_name_ru',
        'is_folder',
        'created_by',
        'deleted_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

}
