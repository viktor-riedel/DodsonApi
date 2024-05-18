<?php

namespace App\Http\Controllers\UserPanel\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockCars\StockCarResource;
use App\Http\Traits\CartTrait;
use App\Models\Car;
use Illuminate\Http\Request;

class UserCartController extends Controller
{
    use CartTrait;

    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->checkCartExist($request->user());

        $cartCars = Car::whereIn('id',
                    $request->user()->cart->cartItems->pluck('car_id')->toArray()
                )
                ->with('carFinance', 'images', 'carAttributes', 'modifications')
                ->get();
        return response()->json([
            'cars' => StockCarResource::collection($cartCars),
            'parts' => [],
        ]);
    }
}
