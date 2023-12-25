<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'is_virtual',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['is_virtual' => 'boolean'];

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

    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class,
            'nomenclature_base_item_pdr_positions_markets',
            'nomenclature_base_item_pdr_positions_id',
            'markets_id'
        );
    }

    public function nomenclatureBaseItemModifications(): HasMany
    {
        return $this->hasMany(NomenclatureBaseItemModification::class);
    }
}
