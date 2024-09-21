<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMeToken extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'oauth_token',
        'oauth_token_secret',
        'oauth_verifier',
        'redirect_url',
        'authorized_by',
        'authorized',
        'environment',
        'access_token',
        'access_token_secret',
    ];

    protected $casts = [
        'authorized' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
