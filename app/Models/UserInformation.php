<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInformation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'phone',
        'address',
        'country',
        'is_wholeseller',
    ];

    protected $casts = [
        'is_wholeseller' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
