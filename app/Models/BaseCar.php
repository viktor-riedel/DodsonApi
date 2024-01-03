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
        'model_year',
        'mileage',
        'engine_type',
        'engine_size',
        'power',
        'fuel',
        'transmission',
        'drivetrain',
        'color',
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
