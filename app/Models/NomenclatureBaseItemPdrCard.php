<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdrCard extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nomenclature_base_item_pdr_position_id',
        'name_eng',
        'name_ru',
        'price_nz_wholesale',
        'price_nz_retail',
        'price_ru_wholesale',
        'price_ru_retail',
        'price_jp_minimum_buy',
        'price_jp_maximum_buy',
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
        return $this->belongsTo(NomenclatureBaseItemPdrPosition::class);
    }
}
