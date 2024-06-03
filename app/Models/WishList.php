<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishList extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wishable_id',
        'wishable_type',
        'user_id'
    ];

    public function wishable(): MorphTo
    {
        return $this->morphTo();
    }
}
