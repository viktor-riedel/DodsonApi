<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Market extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function baseItemPdrPositions(): BelongsToMany
    {
        return $this->belongsToMany(NomenclatureBaseItemPdrPosition::class,
            'nomenclature_base_item_pdr_positions_markets',
            'markets_id', 'nomenclature_base_item_pdr_positions_id');
    }
}
