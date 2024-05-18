<?php

namespace App\Http\Controllers\StockCars\WishList;

use App\Events\StockCars\AddedToWishListEvent;
use App\Events\StockCars\RemovedFromWishListEvent;
use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    public function addWishList(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        if (!$car->wished) {
            $car->wished()->create(['user_id' => $request->user()->id]);
            event(new AddedToWishListEvent($request->user()));
        } else {
            $car->wished()->delete();
            event(new RemovedFromWishListEvent($request->user()));
        }

        return response()->json([], 202);
    }

    public function carWished(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        $wished = $car->wished()->where('user_id', $request->user()->id)->exists();
        return response()->json(['wished' => $wished]);
    }
}
