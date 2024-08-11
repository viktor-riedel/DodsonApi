<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'system',
        'file_name',
        'file_size',
        'file_mime',
        'extension',
        'user_id',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
