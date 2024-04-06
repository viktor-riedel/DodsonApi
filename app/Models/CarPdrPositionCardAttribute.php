<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdrPositionCardAttribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_pdr_position_card_id',
        'color',
        'weight',
        'volume',
        'trademe',
        'drom',
        'avito',
        'dodson',
        'amount',
        'ordered_for_user_id',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function pdrPositionCard(): BelongsTo
    {
        return $this->belongsTo(CarPdrPositionCard::class);
    }

    public function orderedFor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_for_user_id');
    }
}
