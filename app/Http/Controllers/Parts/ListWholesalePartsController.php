<?php

namespace App\Http\Controllers\Parts;

use App\Http\Controllers\Controller;
use App\Http\Resources\Part\WholesalePartAdminResource;
use App\Http\Resources\Part\WholesalePartsAdminResource;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\CarPdrPosition;
use App\Models\CarPdrPositionCard;
use DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListWholesalePartsController extends Controller
{

    use DefaultSellingMapTrait;

    public function list(Request $request): AnonymousResourceCollection
    {
        $makes = [];
        $models = [];
        $years = [];
        if ($request->get('make')) {
            $makes = explode(',', $request->get('make'));
        }
        if ($request->get('model')) {
            $models = explode(',', $request->get('model'));
        }
        if ($request->get('year')) {
            $years = explode(',', $request->get('year'));
        }
        $parts = $request->get('parts');
        $generation = $request->get('generation');
        $engine = $request->get('engine');
        $sortByMake = $request->get('sortByMake');
        $sortByModel = $request->get('sortByModel');
        $sortByYear = $request->get('sortByYear');
        $sortByPrice = $request->get('sortByPrice');
        $search = $request->get('search');
        $country = $request->get('country');

        $sellingPartNames = null;

        if ($parts) {
            $partsIds = explode(',', $parts);
            $sellingPartNames = $this->getPartsNamesByIds($partsIds);
        }

        $parts = CarPdrPosition::with('carPdr', 'carPdr.car',
            'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard', 'client')
            ->where(function($query) use ($makes,
                $models,
                $years,
                $engine,
                $sellingPartNames,
                $search
            )
            {
                $query->when(count($makes), function ($query) use ($makes) {
                    return $query->whereHas('carPdr', function ($query) use ($makes) {
                        return $query->whereHas('car', function ($query) use ($makes) {
                            return $query->whereIn('make', $makes);
                        });
                    });
                });

                $query->when(count($models), function ($query) use ($models) {
                    return $query->whereHas('carPdr', function ($query) use ($models) {
                        return $query->whereHas('car', function ($query) use ($models) {
                            return $query->whereIn('model', $models);
                        });
                    });
                });

                $query->when($years, function ($query) use ($years) {
                    return $query->whereHas('carPdr', function ($query) use ($years) {
                        return $query->whereHas('car', function ($query) use ($years) {
                            return $query->whereHas('carAttributes', function($query) use ($years) {
                                return $query->whereIn('year', $years);
                            });
                        });
                    });
                });

                $query->when($engine, function ($query) use ($engine) {
                    return $query->whereHas('carPdr', function ($query) use ($engine) {
                        return $query->whereHas('car', function ($query) use ($engine) {
                            return $query->whereHas('modifications', function($query) use ($engine) {
                                return $query->where('inner_id', $engine);
                            });
                        });
                    });
                });
                $query->when($sellingPartNames, function ($query) use ($sellingPartNames) {
                    return $query->whereIn('item_name_eng', $sellingPartNames);
                });
                $query->when($search, function ($query) use ($search) {
                    return $query->where('item_name_eng', 'REGEXP', $search)
                            ->orWhere('ic_number', 'REGEXP', $search);
                });

                return $query;
            })
            ->where(function ($query) {
                return $query->whereHas('carPdr', function ($query) {
                    return $query->whereHas('car', function ($query) {
                        return $query->where('virtual', true);
                    });
                });
            })
            ->when($sortByMake, function ($query, $sortByMake) {
                return $query->orderBy(
                    CarPdr::select(['cars.make'])
                        ->whereColumn('car_pdrs.id', '=', 'car_pdr_positions.car_pdr_id')
                        ->join('cars', function (JoinClause $join) use ($sortByMake) {
                            $join->on('cars.id', '=', 'car_id');
                        }), $sortByMake);
            })
            ->when($sortByModel, function ($query, $sortByModel) {
                return $query->orderBy(
                    CarPdr::select(['cars.model'])
                        ->whereColumn('car_pdrs.id', '=', 'car_pdr_positions.car_pdr_id')
                        ->join('cars', function (JoinClause $join) use ($sortByModel) {
                            $join->on('cars.id', '=', 'car_pdrs.car_id');
                        }), $sortByModel);
            })
            ->when($sortByYear, function ($query, $sortByYear) {
                return $query->orderBy(
                    CarPdr::select(['car_attributes.year'])
                        ->whereColumn('car_pdrs.id', '=', 'car_pdr_positions.car_pdr_id')
                        ->join('car_attributes', function (JoinClause $join){
                            $join->on('car_attributes.car_id', '=', 'car_pdrs.car_id')
                                ->whereNotNull('car_attributes.year');
                        }), $sortByYear);
            })
            ->when($sortByPrice, function ($query, $sortByPrice) {
                return $query->orderBy(
                    CarPdrPositionCard::select(['car_pdr_position_card_prices.buying_price'])
                        ->whereColumn('car_pdr_position_cards.car_pdr_position_id',
                            '=', 'car_pdr_positions.id')
                        ->join('car_pdr_position_card_prices', function (JoinClause $join) {
                            $join->on('car_pdr_position_card_prices.car_pdr_position_card_id', '=',
                                'car_pdr_position_cards.id')
                                ->whereNotNull('car_pdr_position_card_prices.buying_price');
                        }), $sortByPrice);
            })
            ->paginate(50);

        return WholesalePartsAdminResource::collection($parts);
    }

    public function get(CarPdrPosition $part): WholesalePartAdminResource
    {
        $part->load('carPdr', 'carPdr.car', 'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard', 'client', 'images');
        return new WholesalePartAdminResource($part);
    }

    public function defaultSellingParts(): AnonymousResourceCollection
    {
        $parts = $this->getDefaultSellingMap();
        return SellingMapItemResource::collection($parts);
    }

    public function makes(): JsonResponse
    {
        $makes = DB::table('cars')
            ->selectRaw('distinct(make)')
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('virtual', true)
            ->orderBy('cars.make')
            ->get();

        return response()->json($makes);
    }

    public function models(string $make): JsonResponse
    {
        $models = DB::table('cars')
            ->selectRaw('distinct(model)')
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('virtual', true)
            ->where('cars.make', $make)
            ->orderBy('cars.model')
            ->get();

        return response()->json($models);
    }

    public function years(string $make): JsonResponse
    {
        $years = DB::table('cars')
            ->selectRaw('distinct(car_attributes.year)')
            ->join('car_attributes', function (JoinClause $join) {
                $join->on('car_attributes.car_id', '=', 'cars.id');
            })
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('virtual', true)
            ->where('cars.make', $make)
            ->whereNotNull('car_attributes.year')
            ->orderBy('car_attributes.year')
            ->get();

        return response()->json($years);
    }

    public function engines(string $make, string $model, string $year): JsonResponse
    {
        $carsIds = DB::table('cars')
            ->selectRaw('distinct(cars.id)')
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->join('car_attributes', function (JoinClause $join) use ($year) {
                $join->on('car_attributes.car_id', '=', 'cars.id');
            })
            ->whereNull('cars.deleted_at')
            ->where('virtual', true)
            ->where('cars.make', $make)
            ->where('cars.model', $model)
            ->where('car_attributes.year', $year)
            ->get()
            ->pluck('id')
            ->toArray();

        $modifications = Car::with('modifications')->whereIn('id', $carsIds)
            ->get()
            ->pluck('modifications');

        return response()->json($modifications);
    }

    public function delete(Request $request, CarPdrPosition $part)
    {
        $part->load('orderItem');
        if (!$part->orderItem) {
            $part->update(['deleted_by' => $request->user()->id]);
            $part->delete();
            return response()->json([]);
        }
        abort(403, 'Part is in order');
    }

    public function update(Request $request, Car $car): JsonResponse
    {
        $car->load('carAttributes');
        $car->update(['chassis' => strtoupper($request->input('chassis'))]);
        $car->carAttributes()->update([
            'chassis' => strtoupper($request->input('chassis')),
            'year' => $request->integer('year'),
            'color' => $request->input('color'),
            'mileage' => $request->integer('mileage'),
        ]);
        return response()->json([]);
    }

    public function prices(Request $request, CarPdrPosition $part)
    {
        $part->load('card', 'card.priceCard');
        $part->card->priceCard()->update([
             'pricing_nz_retail' => $request->integer('pricing_nz_retail'),
             'pricing_nz_wholesale' => $request->integer('pricing_nz_wholesale'),
             'pricing_ru_retail' => $request->integer('pricing_ru_retail'),
             'pricing_ru_wholesale' => $request->integer('pricing_ru_wholesale'),
             'pricing_mng_retail' => $request->integer('pricing_mng_retail'),
             'pricing_mng_wholesale' => $request->integer('pricing_mng_wholesale'),
             'pricing_jp_retail' => $request->integer('pricing_jp_retail'),
             'pricing_jp_wholesale' => $request->integer('pricing_jp_wholesale'),
        ]);
        return response()->json([]);
    }
}
