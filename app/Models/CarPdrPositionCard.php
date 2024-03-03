<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function priceCard(): HasOne
    {
        return $this->hasOne(CarPdrPositionCardPrice::class);
    }

    public function partAttributesCard(): HasOne
    {
        return $this->HasOne(CarPdrPositionCardAttribute::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

}
