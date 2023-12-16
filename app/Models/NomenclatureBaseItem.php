<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureBaseItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'make',
        'model',
        'header',
        'generation',
        'generation_number',
        'year_start',
        'year_stop',
        'month_start',
        'month_stop',
        'preview_image',
        'restyle',
        'not_restyle',
        'doors',
        'body_type',
        'engine_name',
        'engine_type',
        'engine_size',
        'engine_power',
        'transmission_type',
        'drive_train',
        'chassis',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'created_by', 'deleted_by'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'datetime',
        'deleted_by' => 'datetime',
        'restyle' => 'boolean',
        'not_restyle' => 'boolean',
    ];

    public function baseItemPDR(): HasMany
    {
        return $this->hasMany(NomenclatureBaseItemPdr::class);
    }
}
