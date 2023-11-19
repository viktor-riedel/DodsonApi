<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItemPdr extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomenclature_base_item_id',
        'parent_id',
        'item_name_eng',
        'item_name_ru',
        'is_folder',
        'is_deleted',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'created_by', 'deleted_by'];

    public function nomenclatureBaseItemPdrCard(): HasOne
    {
        return $this->hasOne(NomenclatureBaseItemPdrCard::class);
    }

    public function nomenclatureBaseItem(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItem::class);
    }
}
