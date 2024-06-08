<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPositionCard extends Model
{
    use SoftDeletes;

    public const BARCODE_ALGO = 'EAN8';

    protected $fillable = [
        'car_pdr_position_id',
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

    public function position(): BelongsTo
    {
        return $this->belongsTo(CarPdrPosition::class, 'car_pdr_position_id', 'id');
    }

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

    public function modification(): MorphOne
    {
        return $this->morphOne(NomenclatureModification::class, 'modificationable');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(PartComment::class, 'commentable');
    }
}
