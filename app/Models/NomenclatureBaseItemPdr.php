<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdr extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomenclature_base_item_id',
        'nomenclature_base_item_pdr_card_id',
        'nomenclature_base_item_pdr_position_id',
        'parent_id',
        'item_name_eng',
        'item_name_ru',
        'is_folder',
        'is_deleted',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'created_by', 'deleted_by'];

    public function nomenclatureBaseItem(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItem::class);
    }

    public function nomenclatureBaseItemPdrPositions(): HasMany
    {
        return $this->hasMany(NomenclatureBaseItemPdrPosition::class);
    }

    public function nomenclatureBaseItemCard(): HasOne
    {
        return $this->hasOne(NomenclatureBaseItemPdrCard::class);
    }

    public function nomenclatureBaseItemVirtualPosition(): HasOne
    {
        return $this->hasOne(NomenclatureBaseItemPdrPosition::class, 'id', 'nomenclature_base_item_pdr_position_id');
    }
}
