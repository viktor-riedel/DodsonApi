<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use SoftDeletes;

    public const CAR_STATUSES = [
        0 => 'virtual',
        1 => 'in work',
        2 => 'done',
        3 => 'problem',
    ];

    protected $fillable = [
        'parent_inner_id',
        'make',
        'model',
        'car_status',
        'generation',
        'created_by',
        'deleted_by',
    ];

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function carAttributes(): HasOne
    {
        return $this->hasOne(CarAttribute::class);
    }

    public function modification(): HasOne
    {
        return $this->hasOne(CarModification::class);
    }

    public function pdrs(): HasMany
    {
        return $this->hasMany(CarPdr::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

}
