<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseCar extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomenclature_base_item_id',
        'make',
        'model',
        'generation',
        'generation_number',
        'body_type',
        'doors',
        'month_start',
        'month_stop',
        'year_start',
        'year_stop',
        'restyle',
        'not_restyle',
        'header',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function nomenclatureBaseItem(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItem::class);
    }
}
