<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdrPosition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomenclature_base_item_pdr_id',
        'ic_number',
        'oem_number',
        'ic_description',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function nomenclatureBaseItemPdr(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItemPdr::class);
    }

    public function nomenclatureBaseItemPdrCard(): HasOne
    {
        return $this->hasOne(NomenclatureBaseItemPdrCard::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(NomenclatureBaseItemPdrPositionPhoto::class);
    }
}
