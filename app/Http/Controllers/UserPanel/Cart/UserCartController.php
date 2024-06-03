<?php

namespace App\Http\Controllers\UserPanel\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CarItemResource;
use App\Http\Resources\StockCars\StockCarResource;
use App\Http\Traits\CartTrait;
use App\Models\Car;
use App\Models\WishList;
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
                ->get()->each(function($car) use ($request) {
                    $car->buy_with_engine =
                        $request->user()->cart->cartItems->where('car_id', $car->id)
                            ->first()?->with_engine;
                    $car->buy_without_engine =
                        $request->user()->cart->cartItems->where('car_id', $car->id)
                            ->first()?->without_engine;
                    $car->comment =
                        $request->user()->cart->cartItems->where('car_id', $car->id)
                            ->first()?->comment;
            });
        return response()->json([
            'cars' => CarItemResource::collection($cartCars),
            'parts' => [],
        ]);
    }

    public function wishList(Request $request): \Illuminate\Http\JsonResponse
    {
        $cars = Car::whereIn('id',
            WishList::where("user_id", $request->user()->id)
                ->where('wishable_type', 'App\Models\Car')
                ->pluck("wishable_id"))
            ->get();
        return response()->json([
            'cars' => StockCarResource::collection($cars),
            'parts' => [],
        ]);
    }
}
