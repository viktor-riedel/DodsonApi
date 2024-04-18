<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'importable_id',
        'importable_type',
        'imported_id',
        'imported_from',
        'imported_by',
        'comment',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function importable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

}
