<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Matrix\Builder;

class SyncData extends Model
{
    use softDeletes;

    protected $fillable = [
        'syncable_id',
        'syncable_type',
        'document_number',
        'document_date',
        'document_url',
        'document_type',
        'data',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function syncable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

    public function scopeLatestCarsSync($query, $carId): Builder
    {
        return $query->where('syncable_type', 'App\\Models\\Car')
            ->where('syncable_id', $carId)
            ->orderBy('created_at', 'desc');
    }

}
