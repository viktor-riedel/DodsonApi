<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBalanceItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_balance_id',
        'document_name',
        'closing_balance',
    ];

    public function userBalance(): BelongsTo
    {
        return $this->belongsTo(UserBalance::class);
    }

    public function itemDocuments(): HasMany
    {
        return $this->hasMany(UserBalanceItemDocument::class);
    }
}
