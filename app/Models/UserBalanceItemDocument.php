<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBalanceItemDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_balance_item_id',
        'document_description',
        'amount',
    ];

    public function balanceItem(): BelongsTo
    {
        return $this->belongsTo(UserBalanceItem::class);
    }
}
