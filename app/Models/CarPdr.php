<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarPdr extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_id',
        'car_pdr_card_id',
        //base car pdr position below
        'car_pdr_position_id',
        'parent_id',
        'item_name_eng',
        'item_name_ru',
        'is_folder',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function positions(): HasMany
    {
        return $this->hasMany(CarPdrPosition::class);
    }

    public function virtualPosition(): HasOne
    {
        return $this->hasOne(CarPdrPosition::class, 'id', 'car_pdr_position_id');
    }

    public function card(): HasOne
    {
        return $this->hasOne(CarPdrPositionCard::class, 'id', 'car_pdr_card_id');
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
