<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'mobile_phone',
        'landline_phone',
        'company_name',
        'trading_name',
        'address',
        'country',
        'comment',
        'wholesaler',
        'parts_sale_user',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'wholesaler' => 'boolean',
        'parts_sale_user' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
