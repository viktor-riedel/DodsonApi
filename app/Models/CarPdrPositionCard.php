<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPositionCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_inner_id',
        'name_eng',
        'name_ru',
        'comment',
        'description',
        'ic_number',
        'oem_number',
        'created_by',
        'deleted_by',
    ];

    public function priceCard(): HasOne
    {
        return $this->hasOne(CarPdrPositionCardPrice::class);
    }

    public function partAttributesCard(): HasMany
    {
        return $this->hasMany(CarPdrPositionCardAttribute::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

}
