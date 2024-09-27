<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Part extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inner_id',
        'hash_id',
        'stock_number',
        'ic_number',
        'oem_number',
        'ic_description',
        'make',
        'model',
        'year',
        'generation',
        'mileage',
        'amount',
        'item_name_eng',
        'part_group',
        'item_name_ru',
        'item_name_jp',
        'item_name_mng',
        'original_barcode',
        'generated_barcode',
        'price_nzd',
        'actual_price_nzd',
        'standard_price_nzd',
        'comment',
        'color',
    ];

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function modifications(): MorphOne
    {
        return $this->morphOne(NomenclatureModification::class, 'modificationable');
    }

    public function tradeMeListing(): HasOne
    {
        return $this->hasOne(TradeMeListing::class);
    }
}
