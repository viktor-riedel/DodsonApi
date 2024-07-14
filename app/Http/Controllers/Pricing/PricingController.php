<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use App\Http\Traits\CarPdrTrait;
use App\Models\Car;
use App\Models\CarPdrPositionCardPrice;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    use CarPdrTrait;

    public function list(Car $car): \Illuminate\Http\JsonResponse
    {
        $car->load('markets');
        $partsList = $this->getPricingPartsList($car);
        return response()->json([
            'car' => $car,
            'parts' => $partsList,
        ]);
    }

    public function updateSellingPrices(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        $priceCard = CarPdrPositionCardPrice::find($request->integer('id'));
        if ($priceCard) {
            $priceCard->update($request->except('id', 'car_pdr_position_card_id'));
            $priceCard->refresh();
            return response()->json($priceCard);
        }
        return response()->json([], 404);
    }
}
