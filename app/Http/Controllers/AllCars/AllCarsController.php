<?php

namespace App\Http\Controllers\AllCars;

use App\Http\Controllers\Controller;
use App\Http\Resources\Car\CarResource;
use App\Models\Car;

class AllCarsController extends Controller
{
    public function list()
    {
        $cars = Car::with('images', 'carAttributes', 'modification')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return CarResource::collection($cars);
    }
}
