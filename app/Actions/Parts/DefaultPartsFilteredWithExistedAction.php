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

    public function handle(?string $country): Collection
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
        if (!$country && Cache::has('wholesale_parts_all')) {
            return Cache::get('wholesale_parts_all');
        }


        $parts = $this->getDefaultSellingMap();
        $availableParts = CarPdrPosition::with('carPdr', 'carPdr.car',)
            ->where(function($query) use (
                $country
            ) {
                $query->whereHas('carPdr', function ($query) {
                    return $query->whereHas('car', function ($query) {
                        return $query->whereHas('carFinance', function($query) {
                            return $query->where('parts_for_sale', 1);
                        });
                    });
                });

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
            ->where('user_id', Consts::DODSON_USER)
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
            Cache::put('wholesale_parts_all', $parts, now()->addMinutes(15));
        }

        return $parts;
    }
}
