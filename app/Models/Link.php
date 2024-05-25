<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use SoftDeletes;

    public const LINK_TYPES = [
      'YOUTUBE' => 'YOUTUBE',
      'VIMEO' => 'VIMEO',
      'IMAGE' => 'IMAGE',
      'RESOURCE' => 'RESOURCE',
      'FILE' => 'FILE',
    ];
    
    protected $fillable = [
        'linkable_type',
        'linkable_id',
        'url',
        'type',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function linkable(): MorphTo
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

}
