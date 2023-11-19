<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdrCard extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nomenclature_base_item_pdr_id',
        'name',
        'default_price',
        'default_retail_price',
        'default_wholesale_price',
        'default_special_price',
        'wholesale_rus_price',
        'wholesale_nz_price',
        'retail_rus_price',
        'retail_nz_price',
        'special_rus_price',
        'special_nz_price',
        'comment',
        'description',
        'status',
        'condition',
        'tag',
        'yard',
        'bin',
        'is_new',
        'is_scrap',
        'ic_number',
        'oem_number',
        'inner_number',
        'color',
        'weight',
        'extra',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'created_by', 'deleted_by'];

    public function nomenclatureBaseItemPdr(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItemPdr::class);
    }
}
