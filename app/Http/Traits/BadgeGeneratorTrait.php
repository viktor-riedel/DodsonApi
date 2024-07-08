<?php

namespace App\Http\Traits;

use App\Models\Car;
use App\Models\CarPdrPositionCard;

trait BadgeGeneratorTrait
{
    private function generateNextBarcode(?Car $car = null, ?CarPdrPositionCard $card = null): int
    {
        $max = CarPdrPositionCard::max('barcode');
        if ($max < 8000100) {
            $max = 8000100;
        }

        return $max + 1;
    }
}
