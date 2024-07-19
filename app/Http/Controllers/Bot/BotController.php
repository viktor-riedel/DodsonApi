<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Http\Resources\Bot\CarResource;
use App\Models\Car;

class BotController extends Controller
{
    public function car(Car $car): CarResource
    {
        $car->load('images', 'links', 'modifications', 'markets', 'carAttributes');
        return new CarResource($car);
    }

    public function stock(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $cars = Car::with('carFinance', 'images', 'carAttributes', 'modifications', 'markets')
            ->whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })->get();

        return CarResource::collection($cars);
    }
}
