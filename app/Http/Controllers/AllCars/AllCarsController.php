<?php

namespace App\Http\Controllers\AllCars;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableCars\GenerationResource;
use App\Http\Resources\AvailableCars\MakeResource;
use App\Http\Resources\AvailableCars\ModelResource;
use App\Http\Resources\Car\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;

class AllCarsController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $make = $request->get('make', '');
        $model = $request->get('model', '');
        $generation = $request->get('generation', '');
        $cars = Car::with('images', 'carAttributes', 'modification', 'positions')
            ->when($make, function ($query, $make) {
                return $query->where('make', $make);
            })
            ->when($model, function ($query, $model) {
                return $query->where('model', $model);
            })
            ->when($generation, function ($query, $generation) {
                return $query->where('generation', $generation);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return CarResource::collection($cars);
    }

    public function makes(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $makes = Car::orderBy('make')->get()->pluck('make')->unique();
        return MakeResource::collection($makes);
    }

    public function models(string $make): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $models = Car::where('make', $make)
            ->orderBy('model')
            ->get()->pluck('model')->unique();
        return ModelResource::collection($models);
    }

    public function generations(string $make, string $model)
    {
        $generations = Car::where('make', $make)
            ->where('model', $model)
            ->orderBy('generation')
            ->get()->pluck('generation')->unique();
        return GenerationResource::collection($generations);
    }
}
