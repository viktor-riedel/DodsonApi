<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'make',
        'model',
        'generation',
        'preview_image',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'created_by', 'deleted_by'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function baseItemPDR(): HasMany
    {
        return $this->hasMany(NomenclatureBaseItemPdr::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function baseCars(): HasMany
    {
        return $this->hasMany(BaseCar::class);
    }

    public function nomenclaturePositions(): HasManyThrough
    {
        return $this->hasManyThrough(NomenclatureBaseItemPdrPosition::class, NomenclatureBaseItemPdr::class,);
    }

    public function scopeNomenclaturePositionsNotVirtual($builder): HasManyThrough
    {
        return $this->hasManyThrough(NomenclatureBaseItemPdrPosition::class, NomenclatureBaseItemPdr::class,)
            ->where('is_virtual', false);
    }
}
