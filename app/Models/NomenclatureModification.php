<?php

namespace App\Models;

use App\Events\ModelsEvent\NomenclatureCreateEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NomenclatureModification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'modificationable_id',
        'modificationable_type',
        'inner_id',
        'header',
        'generation',
        'gen_number',
        'modification',
        'engine_name',
        'engine_type',
        'engine_size',
        'engine_power',
        'doors',
        'transmission',
        'drive_train',
        'chassis',
        'body_type',
        'image_url',
        'restyle',
        'not_restyle',
        'month_from',
        'month_to',
        'year_from',
        'year_to',
        'years_string',
    ];

    protected $dispatchesEvents = [
      'created' => NomenclatureCreateEvent::class,
    ];

    public function modificationable(): MorphTo
    {
        return $this->morphTo();
    }
}
