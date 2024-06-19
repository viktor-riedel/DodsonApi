<?php

namespace App\Http\Controllers\StockCars;

use App\Events\StockCars\AddedToWishListEvent;
use App\Events\StockCars\RemovedFromWishListEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Resources\StockCars\GenerationResource;
use App\Http\Resources\StockCars\MakeResource;
use App\Http\Resources\StockCars\ModelResource;
use App\Http\Resources\StockCars\StockCarResource;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Models\Car;
use App\Models\CarAttribute;
use App\Models\CarFinance;
use Illuminate\Http\Request;

class StockCarsController extends Controller
{

    use DefaultSellingMapTrait;

    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $searchText = $request->get('search', null);
        $make = $request->get('make', null);
        $model = $request->get('model', null);
        $generation = $request->get('generation', null);
        $sortByMake = $request->get('sortByMake', null);
        $sortByModel = $request->get('sortByModel', null);
        $sortByYear = $request->get('sortByYear', null);
        $sortByPrice = $request->get('sortByPrice', null);
        $country = $request->get('country', null);

        $cars = Car::with('carFinance', 'images', 'carAttributes', 'modifications', 'markets')
                ->when($make, function ($query, $make) {
                    return $query->where('make', $make);
                })
                ->when($model, function ($query, $model) {
                    return $query->where('model', $model);
                })
                ->when($generation, function ($query, $generation) {
                    return $query->where('generation', $generation);
                })
                ->when($country, function($query, $country) {
                    return $query->whereHas('markets', function($q) use ($country) {
                        return $q->where('country_code', $country);
                    });
                })
                ->when($sortByMake, function ($query, $sortByMake) {
                    return $query->orderBy('make', $sortByMake);
                })
                ->when($sortByModel, function ($query, $sortByModel) {
                    return $query->orderBy('model', $sortByModel);
                })
                ->when($sortByYear, function ($query, $sortByYear) {
                    return $query->orderBy(CarAttribute::select('year')
                        ->whereColumn('car_id', 'cars.id'), $sortByYear);
                })
                ->when($sortByPrice, function ($query, $sortByPrice) {
                    return $query->orderBy(CarFinance::select([
                        'price_with_engine_ru',
                    ])
                    ->whereColumn('car_id', 'cars.id'), $sortByPrice);
                })
                ->when($sortByPrice, function ($query, $sortByPrice) {
                    return $query->orderBy(CarFinance::select([
                        'price_without_engine_ru',
                    ])
                    ->whereColumn('car_id', 'cars.id'), $sortByPrice);
                })
                ->whereHas('carFinance', function ($query) {
                    return $query->where('car_is_for_sale', 1);
                })->when($searchText, function ($query, $searchText) {
                return $query->where('make', 'like', '%' . $searchText . '%')
                    ->orWhere('model', 'like', '%' . $searchText . '%')
                    ->orWhere('chassis', 'like', '%' . $searchText . '%');
                })

            ->paginate(20);
        return StockCarResource::collection($cars);
    }

    public function view(Car $car): \Illuminate\Http\JsonResponse
    {
        $car->load('carFinance', 'images', 'links', 'carAttributes', 'modifications');
        return response()->json([
            'car' => new StockCarResource($car),
            'partsList' => SellingMapItemResource::collection($this->getDefaultSellingMap())
        ]);
    }

    public function makes(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $country = $request->get('country', null);
        $makes = Car::whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })
            ->when($country, function ($query, $country) {
                return $query->whereHas('markets', function ($q) use ($country) {
                    return $q->where('country_code', $country);
                });
            })
            ->orderBy('make')
                ->get('make')
                ->unique('make');
        return MakeResource::collection($makes);
    }

    public function models(Request $request, string $make): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $country = $request->get('country', null);
        $makes = Car::whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })
            ->when($country, function ($query, $country) {
                return $query->whereHas('markets', function ($q) use ($country) {
                    return $q->where('country_code', $country);
                });
            })
            ->where('make', $make)
            ->orderBy('model')
            ->get('model')
            ->unique('model');
        return ModelResource::collection($makes);
    }

    public function generations(Request $request, string $make, string $model): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $country = $request->get('country', null);
        $makes = Car::whereHas('carFinance', function ($query) {
                return $query->where('car_is_for_sale', 1);
            })
            ->when($country, function ($query, $country) {
                return $query->whereHas('markets', function ($q) use ($country) {
                    return $q->where('country_code', $country);
                });
            })
            ->where('make', $make)
            ->where('model', $model)
            ->orderBy('generation')
            ->get('generation')
            ->unique('generation');
        return GenerationResource::collection($makes);
    }

    public function years(string $make, string $model)
    {
//        $makes = Car::whereHas('carFinance', function ($query) {
//            return $query->where('car_is_for_sale', 1);
//        })->with('carAttributes')
//            ->where('make', $make)
//            ->where('model', $model)
//            ->orderBy('car_attributes.year')
//            ->get('car_attributes.year')
//            ->unique('car_attributes.year');
//        return YearResource::collection($makes);
        return response()->json([]);
    }

    public function modifications(string $make, string $model, string $generation)
    {

    }
}
