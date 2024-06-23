<?php

namespace App\Http\Controllers\AllCars;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableCars\GenerationResource;
use App\Http\Resources\AvailableCars\MakeResource;
use App\Http\Resources\AvailableCars\ModelResource;
use App\Http\Resources\Car\CarResource;
use App\Models\Car;
use App\Models\CarPdrPositionCardPrice;
use Illuminate\Http\Request;

class AllCarsController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $make = $request->get('make', '');
        $model = $request->get('model', '');
        $generation = $request->get('generation', '');
        $car_status = $request->get('status', -1);
        $text = $request->get('text', '');

        $cars = Car::with(['images', 'carAttributes', 'carFinance',
            'modification', 'positions', 'positions.card',
            'positions.card.priceCard'])
            ->when($make, function ($query) use ($make) {
                return $query->where('make', $make);
            })
            ->when($model, function ($query) use ($model) {
                return $query->where('model', $model);
            })
            ->when($generation, function ($query) use ($generation) {
                return $query->where('generation', $generation);
            })
            ->when($car_status >= 0, function ($query) use ($car_status) {
                return $query->where('car_status', $car_status);
            })
            ->when($text, function ($query) use ($text) {
                return $query->where('make', 'like', "%$text")
                    ->orWhere('model', 'like', "%$text")
                    ->orWhere('chassis', 'like', "%$text")
                    ->orWhere('car_mvr', 'like', "%$text");
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $cars->getCollection()->each(function ($car) {
            $car->parts_price =  (int) $car->carFinance->purchase_price === 0 ?
                $car->positions->sum('card.priceCard.selling_price') :
                $car->carFinance->purchase_price;
            $car->selling_price = $car->positions->sum('card.priceCard.buying_price');
        });

        return CarResource::collection($cars);
    }

    public function currencyList(): \Illuminate\Http\JsonResponse
    {
        return response()->json(CarPdrPositionCardPrice::getCurrenciesJson());
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

    public function generations(string $make, string $model): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $generations = Car::where('make', $make)
            ->where('model', $model)
            ->orderBy('generation')
            ->get()->pluck('generation')->unique();
        return GenerationResource::collection($generations);
    }

    public function statusList(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => Car::getStatusesJson()]);
    }
}
