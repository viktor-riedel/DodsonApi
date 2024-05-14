<?php

namespace App\Http\Controllers\StockCars;

use App\Events\StockCars\AddedToWishListEvent;
use App\Events\StockCars\RemovedFromWishListEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockCars\GenerationResource;
use App\Http\Resources\StockCars\MakeResource;
use App\Http\Resources\StockCars\ModelResource;
use App\Http\Resources\StockCars\StockCarResource;
use App\Models\Car;
use Illuminate\Http\Request;

class StockCarsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $cars = Car::with('carFinance',
                'images', 'carAttributes', 'modifications')
                ->whereHas('carFinance', function ($query) {
                    return $query->where('car_is_for_sale', 1);
                })
                ->paginate(20);

        return StockCarResource::collection($cars);
    }

    public function makes(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $makes = Car::whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })
            ->orderBy('make')
                ->get('make')
                ->unique('make');
        return MakeResource::collection($makes);
    }

    public function models(string $make): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $makes = Car::whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })
            ->where('make', $make)
            ->orderBy('model')
            ->get('model')
            ->unique('model');
        return ModelResource::collection($makes);
    }

    public function generations(string $make, string $model): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $makes = Car::whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })
            ->where('make', $make)
            ->where('model', $model)
            ->orderBy('generation')
            ->get('generation')
            ->unique('generation');
        return GenerationResource::collection($makes);
    }

    public function modifications(string $make, string $model, string $generation)
    {

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

        return response()->json([], 202);
    }

    public function carWished(Request $request, Car $car): \Illuminate\Http\JsonResponse
    {
        $wished = $car->wished()->where('user_id', $request->user()->id)->exists();
        return response()->json(['wished' => $wished]);
    }
}
