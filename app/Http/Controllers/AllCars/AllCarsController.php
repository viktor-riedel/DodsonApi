<?php

namespace App\Http\Controllers\AllCars;

use App\Http\Controllers\Controller;
use App\Http\Resources\Car\CarResource;
use App\Models\Car;
use App\Models\CarPdrPosition;

class AllCarsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $cars = Car::with('images', 'carAttributes', 'modification', 'pdrs')
            ->orderBy('created_at', 'desc')
            ->paginate(20)->each(function ($item) {
                $item->parts_count =
                    CarPdrPosition::whereIn('car_pdr_id', $item->pdrs->pluck('id')->toArray())->count();
            });

        return CarResource::collection($cars);
    }
}
