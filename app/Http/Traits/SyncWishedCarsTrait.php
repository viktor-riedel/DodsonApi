<?php

namespace App\Http\Traits;

use App\Models\WishList;

trait SyncWishedCarsTrait
{
    private function SyncWishedCars(int $carId): void
    {
        WishList::where('wishable_type', 'App\Models\Car')
                ->where('wishable_id', $carId)
                ->delete();
    }
}
