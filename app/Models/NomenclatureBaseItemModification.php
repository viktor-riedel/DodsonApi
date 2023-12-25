<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemModification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomenclature_base_item_pdr_position_id',
        'header',
        'generation',
        'modification',
        'engine_name',
        'engine_type',
        'engine_size',
        'engine_power',
        'doors',
        'transmission',
        'drive_train',
        'chassis',
        'body_type',
        'image_url',
        'restyle',
        'not_restyle',
        'month_from',
        'month_to',
        'year_from',
        'year_to',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];


    public function nomenclatureBaseItemPosition(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItemPdrPosition::class, 'id', 'nomenclature_base_item_pdr_position_id');
    }
}
