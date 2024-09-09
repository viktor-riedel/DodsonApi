<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBalance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'entity_name',
        'closing_balance',
        'balance_items_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function balanceItems(): HasMany
    {
        return $this->hasMany(UserBalanceItem::class);
    }
}
