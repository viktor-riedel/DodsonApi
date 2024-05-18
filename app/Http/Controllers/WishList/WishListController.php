<?php

namespace App\Http\Controllers\WishList;

use App\Events\StockCars\AddedToWishListEvent;
use App\Events\StockCars\RemovedFromWishListEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\WishList\WishListCarResource;
use App\Models\Car;
use App\Models\WishList;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    public function list(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getUserWishList());
    }

    public function addWishList(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        if (!$car->wished) {
            $car->wished()->create(['user_id' => $request->user()->id]);
            event(new AddedToWishListEvent($request->user()));
        } else {
            $car->wished()->delete();
            event(new RemovedFromWishListEvent($request->user()));
        }

        return response()->json($this->getUserWishList());
    }

    private function getUserWishList(): array
    {
        $cars = Car::whereIn('id', WishList::where("user_id", 68)
            ->get()
            ->pluck("wishable_id")
            ->toArray()
        )->get();

        $parts = [];
        return [
            'cars' => WishListCarResource::collection($cars),
            'parts' => $parts,
        ];
    }
}
