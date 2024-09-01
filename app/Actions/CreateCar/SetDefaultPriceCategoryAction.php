<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\CarPdrTrait;
use App\Models\Car;
use App\Models\SellingMapItem;

class SetDefaultPriceCategoryAction
{
    use CarPdrTrait;

    public function handle(Car $car, string $category): void
    {
        $defaultParts = SellingMapItem::whereNotNull('parent_id')->get();
        $carParts = $this->getPartsList($car);
        $priceType = match ($category) {
            'A' => 'price_a_jpy',
            'B' => 'price_b_jpy',
            'C' => 'price_c_jpy',
            default => 'price_a_jpy',
        };
        foreach ($carParts as $part) {
            $defaultSellingPart = $defaultParts->where('item_name_eng', $part->name_eng)->first();
            if ($defaultSellingPart) {
                if ($defaultSellingPart[$priceType]) {
                    $part->card?->priceCard?->update([
                       'buying_price' =>  $defaultSellingPart[$priceType]
                    ]);
                }
            }
        }
    }
}
