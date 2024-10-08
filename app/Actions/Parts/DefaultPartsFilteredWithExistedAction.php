<?php

namespace App\Actions\Parts;

use App\Helpers\Consts;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Models\CarPdrPosition;
use Cache;
use Illuminate\Support\Collection;

class DefaultPartsFilteredWithExistedAction
{
    use DefaultSellingMapTrait;

    public function handle(?string $country, $retail = false): Collection
    {
        if ($country === 'RU' && Cache::has('wholesale_parts_ru')) {
            return Cache::get('wholesale_selling_parts_ru');
        }
        if ($country === 'NZ' && Cache::has('wholesale_parts_nz')) {
            return Cache::get('wholesale_selling_parts_nz');
        }
        if ($country === 'MNG' && Cache::has('wholesale_parts_mng')) {
            return Cache::get('wholesale_selling_parts_mng');
        }
        if (!$country) {
            if ($retail && Cache::has('retail_parts_all')) {
                return Cache::get('retail_parts_all');
            }
            if (Cache::has('wholesale_parts_all')) {
                return Cache::get('wholesale_parts_all');
            }
        }


        $parts = $this->getDefaultSellingMap();
        $availableParts = CarPdrPosition::with('carPdr', 'carPdr.car')
                ->where(function($query) use ($country) {
                    $query->when($country, function ($query) use ($country) {
                        $query->whereHas('carPdr', function ($query) use ($country) {
                            return $query->whereHas('car', function ($query) use ($country) {
                                return $query->whereHas('markets', function ($query) use ($country) {
                                    return $query->where('country_code', $country);
                                });
                            });
                        });
                    });

                return $query;
            })
            ->where('user_id', Consts::getPartsSaleUserId())
            ->get()
            ->pluck('item_name_eng')
            ->toArray();

        $availableParts = array_unique($availableParts);

        $parts->each(function ($parts) use ($availableParts) {
            $parts->items->each(function ($item) use ($availableParts) {
                $item->available = false;
                foreach($availableParts as $availablePart) {
                    if ($item->item_name_eng === $availablePart) {
                        $item->available = true;
                    }
                }
            });
        });

        if ($country === 'RU') {
            Cache::put('wholesale_selling_parts_ru', $parts, now()->addMinutes(15));
        }
        if ($country === 'NZ') {
            Cache::put('wholesale_selling_parts_nz', $parts, now()->addMinutes(15));
        }
        if ($country === 'MNG') {
            Cache::put('wholesale_selling_parts_mng', $parts, now()->addMinutes(15));
        }
        if (!$country) {
            if ($retail) {
                Cache::put('retail_parts_all', $parts, now()->addMinutes(15));
            } else {
                Cache::put('wholesale_parts_all', $parts, now()->addMinutes(15));
            }
        }

        return $parts;
    }
}
