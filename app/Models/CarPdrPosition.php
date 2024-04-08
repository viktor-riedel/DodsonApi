<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function carPdr(): BelongsTo
    {
        return $this->belongsTo(CarPdr::class);
    }

    public function card(): HasOne
    {
        return $this->hasOne(CarPdrPositionCard::class);
    }

    public function modification(): MorphMany
    {
        return $this->morphMany(NomenclatureModification::class, 'modificationable');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
}
