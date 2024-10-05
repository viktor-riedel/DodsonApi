<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPosition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_pdr_id',
        'user_id',
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

    public function modification(): MorphOne
    {
        return $this->MorphOne(NomenclatureModification::class, 'modificationable');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'part_id', 'id');
    }

    public function tradeMeListing(): HasOne
    {
        return $this->hasOne(TradeMeListing::class);
    }
}
