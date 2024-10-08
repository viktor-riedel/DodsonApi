<?php

namespace App\Http\Controllers\StockParts;

use App\Actions\Parts\DefaultPartsFilteredWithExistedAction;
use App\Helpers\Consts;
use App\Http\Controllers\Controller;
use App\Http\Resources\Part\MakeResource;
use App\Http\Resources\Part\ModelResource;
use App\Http\Resources\Part\RetailPartResource;
use App\Http\Resources\Part\ViewRetailPartResource;
use App\Http\Resources\Part\YearResource;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Traits\SystemAccountTrait;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\CarPdrPosition;
use App\Models\CarPdrPositionCard;
use App\Models\NomenclatureBaseItem;
use App\Models\SellingMapItem;
use DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockPartsRetailController extends Controller
{

    use SystemAccountTrait;

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

        $search = $request->get('search');
        $sellingParts = $request->get('parts');
        $engine = $request->get('engine');
        $sortByMake = $request->get('sortByMake');
        $sortByModel = $request->get('sortByModel');
        $sortByYear = $request->get('sortByYear');
        $sortByPrice = $request->get('sortByPrice');

        $sellingPartNames = null;

        if ($sellingParts) {
            $partsIds = explode(',', $sellingParts);
            $sellingPartNames = $this->getPartsNamesByIds($partsIds);
        }

        $parts = CarPdrPosition::with('carPdr', 'carPdr.car',
            'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard', 'carPdr.car.carFinance',
            'carPdr.car.images', 'images')
            ->where(function($query) use ($makes,
                $models,
                $years,
                $engine,
                $sellingPartNames,
                $generations,
                $search
            ) {
                $query->whereHas('carPdr', function ($query) {
                    return $query->whereHas('car', function ($query) {
                        return $query->where('virtual_retail', true);
                    });
                });

                $query->whereHas('card', function($query) {
                    return $query->whereHas('priceCard', function($query) {
                        return $query->where('selling_price', '>', 0);
                    });
                });

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

                $query->when($search, function ($query) use ($search) {
                    return $query->where('item_name_eng', 'REGEXP' , $search)
                        ->orWhere('ic_number', 'REGEXP' , $search)
                        ->orWhere('oem_number', 'REGEXP' , $search);
                });

                $query->when($sellingPartNames, function ($query) use ($sellingPartNames) {
                    return $query->whereIn('item_name_eng', $sellingPartNames);
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
                    CarPdrPositionCard::select(['car_pdr_position_card_prices.selling_price'])
                        ->whereColumn('car_pdr_position_cards.car_pdr_position_id',
                            '=', 'car_pdr_positions.id')
                        ->join('car_pdr_position_card_prices', function (JoinClause $join) {
                            $join->on('car_pdr_position_card_prices.car_pdr_position_card_id', '=',
                                'car_pdr_position_cards.id')
                                ->whereNotNull('car_pdr_position_card_prices.selling_price');
                        }), $sortByPrice);
            })
            ->when(!$sortByMake && !$sortByModel && !$sortByYear && !$sortByPrice, function ($query) {
                return $query->orderBy(
                    CarPdr::select(['car_attributes.year'])
                        ->whereColumn('car_pdrs.id', '=', 'car_pdr_positions.car_pdr_id')
                        ->join('car_attributes', function (JoinClause $join){
                            $join->on('car_attributes.car_id', '=', 'car_pdrs.car_id')
                                ->whereNotNull('car_attributes.year');
                        }), 'asc');
            })
            ->whereNull('user_id')
            ->orWhere('user_id', Consts::getPartsSaleUserId())
            ->paginate(60);

        return RetailPartResource::collection($parts);
    }

    public function get(CarPdrPosition $part): ViewRetailPartResource
    {
        $part->load('carPdr', 'carPdr.car', 'images', 'card', 'card.priceCard');
        return new ViewRetailPartResource($part);
    }

    public function similar(CarPdrPosition $part): AnonymousResourceCollection
    {
        $mvr = $part->carPdr->car->car_mvr;
        $parts = CarPdrPosition::with('carPdr', 'carPdr.car',
            'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard', 'carPdr.car.carFinance',
            'carPdr.car.images', 'images')
            ->where(function($query) use ($mvr) {
                $query->whereHas('carPdr', function ($query) use ($mvr) {
                    return $query->whereHas('car', function ($query) use ($mvr) {
                        return $query->where('virtual_retail', true)
                            ->where('car_mvr', $mvr);
                    });
                });
                $query->whereHas('card', function($query) {
                    return $query->whereHas('priceCard', function($query) {
                        return $query->where('selling_price', '>', 0);
                    });
                });
            })
            ->where('id', '!=', $part->id)
            ->get();
        return RetailPartResource::collection($parts);
    }

    public function defaultPartsList(): AnonymousResourceCollection
    {
        $parts = app()->make(DefaultPartsFilteredWithExistedAction::class)->handle('', true);
        return SellingMapItemResource::collection($parts);
    }

    public function makes(): AnonymousResourceCollection
    {
        $makes = DB::table('cars')
            ->selectRaw('distinct(make)')
            ->where('make', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('make')
            ->where('virtual_retail', true)
            ->orderBy('make')
            ->get();

        return MakeResource::collection($makes);
    }

    public function models(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make');
        $models = DB::table('cars')
            ->selectRaw('distinct(model)')
            ->where('make', '=', $make)
            ->where('model', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('model')
            ->where('virtual_retail', true)
            ->orderBy('model')
            ->get();

        return ModelResource::collection($models);
    }

    public function years(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make');
        $model = $request->get('model');
        $years = DB::table('cars')
            ->selectRaw('distinct(car_attributes.year)')
            ->join('car_attributes', 'cars.id', '=', 'car_attributes.car_id')
            ->where('cars.make', '=', $make)
            ->where('cars.model', $model)
            ->whereNull('cars.deleted_at')
            ->whereNotNull('cars.model')
            ->where('cars.virtual_retail', true)
            ->orderBy('car_attributes.year')
            ->get();
        return YearResource::collection($years);
    }

    public function generations(Request $request, string $make, string $model): JsonResponse
    {
        $ids = DB::table('cars')
            ->selectRaw('distinct nomenclature_base_items.id')
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
            ->join('car_pdr_positions', function (JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->whereNull('cars.deleted_at')
            ->where('cars.virtual_retail', true)
            ->where('cars.make', $make)
            ->where('cars.model', $model)
            ->orderBy('nomenclature_base_items.id')
            ->get()
            ->pluck('id')
            ->toArray();

        $generations = [];

        NomenclatureBaseItem::with('modifications')
            ->whereIn('id', $ids)
            ->get()
            ->each(function ($modification) use (&$generations) {
                $monthStart = $modification->modifications->min("month_from");
                $monthEnd = $modification->modifications->max("month_to");
                $yearFrom = $modification->modifications->min("year_from");
                $yearTo = $modification->modifications->max("year_to");
                $yearsString = $monthStart.'.'.$yearFrom.' - '.$monthEnd.'.'.$yearTo;

                $generations[] = [
                    'generation_number' => (int) $modification->generation,
                    'years_string' => $yearsString,
                ];
            });
        return response()->json($generations);
    }

    public function engines(Request $request, string $make, string $model, string $year): JsonResponse
    {
        $carsIds = DB::table('cars')
            ->selectRaw('distinct(cars.id)')
            ->join('car_finances', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_finances.car_id')
                    ->where('car_finances.parts_for_sale', 1);
            })
            ->join('car_pdrs', function (JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function (JoinClause $join) {
                $join->on('car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->join('car_attributes', function (JoinClause $join) use ($year) {
                $join->on('car_attributes.car_id', '=', 'cars.id');
            })
            ->whereNull('cars.deleted_at')
            ->where('car_pdr_positions.user_id', Consts::getPartsSaleUserId())
            ->where('cars.make', $make)
            ->where('cars.model', $model)
            ->where('cars.virtual_retail', true)
            ->where('car_attributes.year', $year)
            ->get()
            ->pluck('id')
            ->toArray();

        $modifications = Car::with('modifications')->whereIn('id', $carsIds)
            ->get()
            ->pluck('modifications');

        return response()->json($modifications);
    }

    private function getPartsNamesByIds(array $ids): array
    {
        return SellingMapItem::whereIn('id', $ids)
            ->where('parent_id', '>', 0)
            ->get()
            ->pluck('item_name_eng')
            ->toArray();
    }
}
