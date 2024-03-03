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
        $cars = Car::with('images', 'carAttributes', 'modification', 'positions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return CarResource::collection($cars);
    }
}
