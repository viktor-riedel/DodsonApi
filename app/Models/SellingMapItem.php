<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellingMapItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'item_name_eng',
        'item_name_ru',
        'comment',
        'price_jpy',
        'price_rub',
        'price_nzd',
        'price_mng',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(self::class,'parent_id', $this->id);
    }
}
