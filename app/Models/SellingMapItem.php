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
        'price_a_jpy',
        'price_b_jpy',
        'price_c_jpy',
        'price_d_jpy',
        'price_e_jpy',
        'price_f_jpy',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(self::class,'parent_id', $this->id);
    }
}
