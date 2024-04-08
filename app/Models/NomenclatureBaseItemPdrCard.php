<?php

namespace App\Models;

use App\Http\Traits\InnerIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdrCard extends Model
{
    use SoftDeletes, InnerIdTrait;
    
    protected $fillable = [
        'inner_id',
        'nomenclature_base_item_pdr_position_id',
        'name_eng',
        'name_ru',
        'price_nz_wholesale',
        'price_nz_retail',
        'price_ru_wholesale',
        'price_ru_retail',
        'price_jp_minimum_buy',
        'price_jp_maximum_buy',
        'price_jp_wholesale',
        'price_jp_retail',
        'minimum_threshold_nz_retail',
        'minimum_threshold_nz_wholesale',
        'minimum_threshold_ru_retail',
        'minimum_threshold_ru_wholesale',
        'minimum_threshold_jp_retail',
        'minimum_threshold_jp_wholesale',
        'price_mng_retail',
        'price_mng_wholesale',
        'minimum_threshold_mng_retail',
        'minimum_threshold_mng_wholesale',
        'needs',
        'nz_needs',
        'ru_needs',
        'mng_needs',
        'jp_needs',
        'volume',
        'trademe',
        'drom',
        'avito',
        'dodson',
        'nova',
        'comment',
        'description',
        'ic_number',
        'oem_number',
        'color',
        'weight',
        'delivery_price_nz',
        'delivery_price_ru',
        'pinnacle_price',
        'nz_team_price',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'created_by', 'deleted_by'];

    protected $casts = [
        'trademe' => 'boolean',
        'drom' => 'boolean',
        'avito' => 'boolean',
        'dodson' => 'boolean',
        'nova' => 'boolean',
    ];

    public function nomenclatureBaseItemPdrPosition(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItemPdrPosition::class)->withTrashed();
    }

    public function getIsDeletedAttribute(): bool
    {
        return $this->nomenclatureBaseItemPdrPosition->trashed();
    }

    public function getNomenclatureBaseItemAttribute(): NomenclatureBaseItem
    {
        return $this->nomenclatureBaseItemPdrPosition->nomenclatureBaseItemPdr->nomenclatureBaseItem;
    }
}
