<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportedCar extends Model
{
    use SoftDeletes;

    public const IMPORTED_FROM_CAPARTS = 'CAPARTS';
    public const IMPORTED_FROM_PINNACLE = 'PINNACLE';

    protected $fillable = [
        'car_id',
        'external_id',
        'imported_from',
        'date_import',
        'importedBy',
        'lot_number',
        'auction_name',
        'warehouse',
        'stock_number',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'importedBy', 'id');
    }
}
