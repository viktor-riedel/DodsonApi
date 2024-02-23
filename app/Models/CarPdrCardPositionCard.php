<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrCardPositionCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_inner_id',
        'car_pdr_position_id',
        'name_eng',
        'name_ru',
        'comment',
        'description',
        'ic_number',
        'oem_number',
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
