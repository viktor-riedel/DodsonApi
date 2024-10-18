<?php

namespace App\Models;

use App\Http\Traits\DefaultSellingMapTrait;
use Cloudinary\Tag\VideoTag;
use Cloudinary\Transformation\Delivery;
use Cloudinary\Transformation\Format;
use Cloudinary\Transformation\Resize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Car extends Model
{
    use SoftDeletes, DefaultSellingMapTrait;

    public const WITH_ENGINE = 'WITH_ENGINE';
    public const WITHOUT_ENGINE = 'WITHOUT_ENGINE';

    public const CAR_STATUSES = [
        0 => 'virtual',
        1 => 'in work',
        3 => 'dismantling',
        4 => 'dismantled',
        5 => 'car for parts',
        2 => 'done',
    ];

    protected $fillable = [
        'parent_inner_id',
        'make',
        'model',
        'chassis',
        'car_status',
        'generation',
        'car_mvr',
        'comment',
        'created_by',
        'deleted_by',
        'contr_agent_name',
        'virtual',
        'virtual_retail',
        'ignore_modification',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'ignore_modification' => 'boolean',
    ];

    protected $hidden = ['updated_at', 'deleted_at'];

    public static function getStatusesJson(): array
    {
        $statuses = [];
        foreach (self::CAR_STATUSES as $id => $status) {
            $statuses[] = [
                'id' => $id,
                'status' => $status,
            ];
        }
        return $statuses;
    }

    public function images(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable')
            ->where('mime', '!=', 'video/mp4');
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable')
            ->where('mime', '=', 'video/mp4');
    }

    public function getFormatVideosAttribute(): Collection
    {
        $videos = collect();
        $this->videos->each(function (MediaFile $file) use ($videos) {
            $videos->push(
                [
                    'id' => $file->id,
                    'tag' => (new VideoTag($file->folder_name . '/' . $file->original_file_name))
                        ->resize(Resize::scale()->height(260))
                        ->delivery(Delivery::format(Format::auto()))
                        ->setAttributes(['controls'])
                        ->toTag(),
                ]);
        });
        return $videos;
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Link::class, 'linkable');
    }

    public function carAttributes(): HasOne
    {
        return $this->hasOne(CarAttribute::class);
    }

    public function modification(): HasOne
    {
        return $this->hasOne(CarModification::class);
    }

    public function modifications(): MorphOne
    {
        return $this->morphOne(NomenclatureModification::class, 'modificationable');
    }

    public function syncedPartsData(): MorphMany
    {
        return $this->morphMany(SyncData::class, 'syncable');
    }

    public function latestSyncData(): MorphOne
    {
        return $this->morphOne(SyncData::class, 'syncable')->latest();
    }

    public function pdrs(): HasMany
    {
        return $this->hasMany(CarPdr::class);
    }

    public function positions(): HasManyThrough
    {
        return $this->hasManyThrough(CarPdrPosition::class, CarPdr::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

    public function importedCar(): HasOne
    {
        return $this->hasOne(ImportedCar::class);
    }

    public function importItem(): MorphOne
    {
        return $this->morphOne(ImportItem::class, 'importable');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusUpdateLog::class);
    }

    public function carFinance(): HasOne
    {
        return $this->hasOne(CarFinance::class);
    }

    public function wished(): MorphOne
    {
        return $this->morphOne(WishList::class, 'wishable');
    }

    public function markets(): HasMany
    {
        return $this->hasMany(CarMarket::class);
    }

    public function baseCar(): BelongsTo
    {
        return $this->belongsTo(NomenclatureBaseItem::class, 'parent_inner_id', 'inner_id');
    }

    public function getHasActiveOrderAttribute(): bool
    {
        return OrderItem::where('car_id', $this->id)->count() === $this->getDefaultMapItemsCount();
    }
}
