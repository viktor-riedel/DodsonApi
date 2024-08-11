<?php

namespace App\Http\Controllers\StockParts;

use App\Actions\Parts\DefaultPartsFilteredWithExistedAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Part\WholesaleIndividualPartResource;
use App\Http\Resources\Part\WholesalePartResource;
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

class StockPartWholesaleController extends Controller
{
    use DefaultSellingMapTrait;

    private const DODSON_USER = 135;

    public function list(Request $request): AnonymousResourceCollection
    {
        $makes = [];
        $models = [];
        $years = [];
        $generations = [];

        if ($request->get('make')) {
            $makes = explode(',', $request->get('make'));
        }
        if ($request->get('model')) {
            $models = explode(',', $request->get('model'));
        }
        if ($request->get('year')) {
            $years = explode(',', $request->get('year'));
        }
        if ($request->get('generation')) {
            $generations = explode(',', $request->get('generation'));
        }
        $sellingParts = $request->get('parts');
        $engine = $request->get('engine');
        $sortByMake = $request->get('sortByMake');
        $sortByModel = $request->get('sortByModel');
        $sortByYear = $request->get('sortByYear');
        $sortByPrice = $request->get('sortByPrice');
        $country = $request->get('country');

        $sellingPartNames = null;

        if ($sellingParts) {
            $partsIds = explode(',', $sellingParts);
            $sellingPartNames = $this->getPartsNamesByIds($partsIds);
        }

        $parts = CarPdrPosition::with('carPdr', 'carPdr.car',
                'carPdr.car.carAttributes', 'carPdr.car.modifications',
                'card', 'card.priceCard', 'carPdr.car.carFinance',
                'carPdr.car.markets', 'carPdr.car.images', 'images')
            ->where(function($query) use ($makes,
                    $models,
                    $years,
                    $engine,
                    $sellingPartNames,
                    $country,
                    $generations
            ) {
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

                $query->whereHas('carPdr', function ($query) {
                    return $query->whereHas('car', function ($query) {
                        return $query->whereHas('carFinance', function($query) {
                            return $query->where('parts_for_sale', 1);
                        });
                    });
                });

                $query->when($country, function ($query) use ($country) {
                    $query->whereHas('carPdr', function ($query) use ($country) {
                        return $query->whereHas('car', function ($query) use ($country) {
                            return $query->whereHas('markets', function ($query) use ($country) {
                                return $query->where('country_code', $country);
                            });
                        });
                    });
                });

                $query->when(count($generations), function ($query) use ($generations) {
                    return $query->whereHas('carPdr', function ($query) use ($generations) {
                        return $query->whereHas('car', function ($query) use ($generations) {
                            return $query->whereHas('baseCar', function($query) use ($generations) {
                               return $query->whereIn('generation', $generations);
                            });
                        });
                    });
                });

                return $query;
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
            ->where('user_id', self::DODSON_USER)
            ->paginate(50);

        return WholesalePartResource::collection($parts);
    }

    public function get(CarPdrPosition $part): WholesaleIndividualPartResource
    {
        $part->load(
            'carPdr',
            'carPdr.car',
            'carPdr.car.carAttributes',
            'carPdr.car.modifications',
            'carPdr.car.images',
            'card',
            'images',
            'card.priceCard');
        return new WholesaleIndividualPartResource($part);
    }

    public function defaultPartsList(Request $request): AnonymousResourceCollection
    {
        $country = $request->get('country');
        $parts = app()->make(DefaultPartsFilteredWithExistedAction::class)->handle($country);
        return SellingMapItemResource::collection($parts);
    }

    public function makes(Request $request): JsonResponse
    {
        $country = $request->get('country');
        $makes = DB::table('cars')
            ->selectRaw('distinct(make)')
            ->join('car_finances', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_finances.car_id')
                    ->where('car_finances.parts_for_sale', 1);
            })
            ->when($country, function ($query) use ($country) {
                $query->join('car_markets', function (JoinClause $join) use ($country) {
                    $join->on('car_markets.car_id', '=', 'cars.id')
                        ->where('car_markets.country_code', $country);
                });
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
            ->where('car_pdr_positions.user_id', self::DODSON_USER)
            ->orderBy('cars.make')
            ->get();

        return response()->json($makes);
    }

    public function models(Request $request, string $make): JsonResponse
    {
        $country = $request->get('country');
        $models = DB::table('cars')
            ->selectRaw('distinct(model)')
            ->join('car_finances', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_finances.car_id')
                    ->where('car_finances.parts_for_sale', 1);
            })
            ->when($country, function ($query) use ($country) {
                $query->join('car_markets', function (JoinClause $join) use ($country) {
                    $join->on('car_markets.car_id', '=', 'cars.id')
                        ->where('car_markets.country_code', $country);
                });
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
            ->where('car_pdr_positions.user_id', self::DODSON_USER)
            ->where('cars.make', $make)
            ->orderBy('cars.model')
            ->get();

        return response()->json($models);
    }

    public function years(Request $request, string $make): JsonResponse
    {
        $country = $request->get('country');
        $years = DB::table('cars')
            ->selectRaw('distinct(car_attributes.year)')
            ->join('car_finances', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_finances.car_id')
                    ->where('car_finances.parts_for_sale', 1);
            })
            ->when($country, function ($query) use ($country) {
                $query->join('car_markets', function (JoinClause $join) use ($country) {
                    $join->on('car_markets.car_id', '=', 'cars.id')
                        ->where('car_markets.country_code', $country);
                });
            })
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
            ->where('car_pdr_positions.user_id', self::DODSON_USER)
            ->where('cars.make', $make)
            ->whereNotNull('car_attributes.year')
            ->orderBy('car_attributes.year')
            ->get();

        return response()->json($years);
    }

    public function generations(Request $request, string $make, string $model): JsonResponse
    {
        $country = $request->get('country');
        $generations = DB::table('cars')
            ->selectRaw('distinct(nomenclature_base_items.generation)')
            ->join('car_finances', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_finances.car_id')
                    ->where('car_finances.parts_for_sale', 1);
            })
            ->when($country, function ($query) use ($country) {
                $query->join('car_markets', function (JoinClause $join) use ($country) {
                    $join->on('car_markets.car_id', '=', 'cars.id')
                        ->where('car_markets.country_code', $country);
                });
            })
            ->join('nomenclature_base_items', function (JoinClause $join) {
                $join->on('nomenclature_base_items.inner_id', '=', 'cars.parent_inner_id');
            })
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
            ->where('car_pdr_positions.user_id', self::DODSON_USER)
            ->where('cars.make', $make)
            ->where('cars.model', $model)
            ->orderBy('nomenclature_base_items.generation')
            ->get();

        return response()->json($generations);
    }

    public function engines(Request $request, string $make, string $model, string $year): JsonResponse
    {
        $country = $request->get('country');
        $carsIds = DB::table('cars')
            ->selectRaw('distinct(cars.id)')
            ->join('car_finances', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_finances.car_id')
                    ->where('car_finances.parts_for_sale', 1);
            })
            ->when($country, function ($query) use ($country) {
                $query->join('car_markets', function (JoinClause $join) use ($country) {
                    $join->on('car_markets.car_id', '=', 'cars.id')
                        ->where('car_markets.country_code', $country);
                });
            })
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
            ->where('car_pdr_positions.user_id', self::DODSON_USER)
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
}
