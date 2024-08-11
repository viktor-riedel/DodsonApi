<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Part extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inner_id',
        'stock_number',
        'ic_number',
        'ic_description',
        'make',
        'model',
        'year',
        'mileage',
        'amount',
        'item_name_eng',
        'item_name_ru',
        'item_name_jp',
        'item_name_mng',
        'original_barcode',
        'generated_barcode',
        'price_jpy',
        'price_nzd',
        'price_mng',
        'comment',
    ];

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function modifications(): MorphOne
    {
        return $this->morphOne(NomenclatureModification::class, 'modificationable');
    }
}
