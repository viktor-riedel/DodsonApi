<?php

namespace App\Http\Controllers\Parts;

use App\Http\Controllers\Controller;
use App\Http\Resources\Part\DocumentResource;
use App\Http\Resources\Part\PartsDocumentResource;
use App\Http\Resources\Part\WholesalePartAdminResource;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureBaseItemPdrPosition;
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
        $search = $request->get('search');

        $documents = DB::table('cars')
            ->selectRaw('
                distinct cars.car_mvr,
                count(car_pdr_positions.id) as parts_count,
                cars.created_by,
                (select group_concat(distinct contr_agent_name) from cars c where c.car_mvr = cars.car_mvr) as contr_agent_name,
                DATE(cars.created_at) as created_at
            ')
            ->join('car_pdrs',function(JoinClause $join) {
                $join->on('cars.id','=','car_pdrs.car_id')
                    ->whereNull('car_pdrs.deleted_at');
            })
            ->join('car_pdr_positions', function(JoinClause $join) {
                $join->on('car_pdrs.id','=','car_pdr_positions.car_pdr_id')
                    ->whereNull('car_pdr_positions.deleted_at');
            })
            ->when($search, function ($query, $search) {
                $query->where('cars.car_mvr', 'LIKE', "%$search%");
            })
            ->where('cars.virtual', true)
            ->where('cars.virtual_retail', false)
            ->orderBy('created_at', 'desc')
            ->groupBy('cars.car_mvr')
            ->groupBy('cars.created_by')
            ->groupBy('created_at')
            ->paginate(50);


        return DocumentResource::collection($documents);
    }

    public function document(string $mvr): AnonymousResourceCollection
    {
        $parts = CarPdrPosition::with('card', 'card.priceCard', 'carPdr.car',
            'carPdr.car.modifications', 'carPdr.car.createdBy', 'client', 'carPdr.car.carFinance')
            ->whereHas('carPdr', function($q) use ($mvr) {
                return $q->whereHas('car', function($q) use ($mvr) {
                    return $q->where('car_mvr', $mvr);
                });
            })->get();
        return PartsDocumentResource::collection($parts);
    }

    public function get(CarPdrPosition $part): WholesalePartAdminResource
    {
        $part->load('carPdr', 'carPdr.car', 'carPdr.car.markets',
            'carPdr.car.carFinance', 'carPdr.car.carAttributes',
            'carPdr.car.modifications', 'card', 'card.priceCard', 'client',
            'images');
        if ($part->carPdr->car->markets) {
            $part->carPdr->car->markets->transform(function($market) {
                return [
                    'name' => findCountryByCode($market->country_code),
                    'country_code' => $market->country_code,
                ];
            });
        }
        $part->original_card = null;
        if ($part->ic_number) {
            $part->original_card = NomenclatureBaseItemPdrCard::where('ic_number', $part->ic_number)
                ->where('description', $part->ic_description)
                ->where('name_eng', $part->item_name_eng)
                ->first();
        }
        //find price card
        return new WholesalePartAdminResource($part);
    }

    public function icNumbers(CarPdrPosition $part): JsonResponse
    {
        $part->load('carPdr', 'carPdr.car', 'carPdr.car.modifications');
        $modificationId = $part->carPdr->car->modifications->inner_id;
        $modificationIds = [];
        if ($part->carPdr->car->ignore_modification) {
            NomenclatureBaseItemPdrPosition::with('modifications')
                ->whereHas('nomenclatureBaseItemPdr', function($q) use ($part) {
                    return $q->whereHas('nomenclatureBaseItem', function($q) use ($part) {
                        return $q->where('make', $part->carPdr->car->make)
                            ->where('model', $part->carPdr->car->model)
                            ->where('generation', $part->carPdr->car->generation);
                    })->where('item_name_eng', $part->item_name_eng);
                })
                ->where('ic_number', '!=', 'virtual')
                ->get()
                ->each(function($position) use (&$modificationIds) {
                    $position->modifications->each(function($modification) use (&$modificationIds) {
                        $modificationIds[] = $modification->inner_id;
                    });
                });
        }
        $items = NomenclatureBaseItem::with(
                'nomenclaturePositions',
                'nomenclaturePositions.nomenclatureBaseItemPdrCard')
            ->when($modificationId, function ($query) use ($modificationId) {
                return $query->whereHas('modifications', function ($query) use ($modificationId) {
                    return $query->where('inner_id', $modificationId);
                });
            })
            ->when($part->carPdr->car->ignore_modification, function ($query) use ($modificationIds) {
                return $query->whereHas('modifications', function ($query) use ($modificationIds) {
                    return $query->whereIn('inner_id', $modificationIds);
                });
            })
            ->get()
            ->pluck('nomenclaturePositions')
            ->flatten();
        $cards = collect();
        foreach($items as $item) {
            if ($item->nomenclatureBaseItemPdrCard->ic_number && $item->nomenclatureBaseItemPdrCard->name_eng === $part->item_name_eng) {
                $cards->push($item->nomenclatureBaseItemPdrCard);
            }
        }
        return response()->json($cards);
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
            ->where('cars.virtual_retail', false)
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
            ->where('virtual_retail', false)
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
            ->where('virtual_retail', false)
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
            ->where('virtual_retail', false)
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
        $car->load('carAttributes', 'carFinance');
        $car->markets()->delete();
        foreach($request->input('markets') as $market) {
            $car->markets()->create([
                'country_code' => $market['country_code'],
            ]);
        }
        $car->update([
            'chassis' => strtoupper($request->input('chassis')),
        ]);
        $car->carFinance()->update([
            'parts_for_sale' => (bool) $request->input('parts_for_sale'),
        ]);
        $car->carAttributes()->update([
            'chassis' => strtoupper($request->input('chassis')),
            'year' => $request->integer('year'),
            'color' => $request->input('color'),
            'mileage' => $request->integer('mileage'),
        ]);
        return response()->json([]);
    }

    public function attributes(Request $request, CarPdrPosition $part): JsonResponse
    {
        $part->load('card');
        $part->update([
            'ic_number' => $request->input('ic_number'),
            'ic_description' => $request->input('ic_description'),
        ]);
        $part->card()->update([
            'ic_number' => $request->input('ic_number'),
            'description' => $request->input('ic_description'),
        ]);
        return response()->json([]);
    }

    public function prices(Request $request, CarPdrPosition $part): JsonResponse
    {
        $part->load('card', 'card.priceCard');
        $part->card->priceCard()->update([
             'price_nz_retail' => $request->integer('price_nz_retail'),
             'price_nz_wholesale' => $request->integer('price_nz_wholesale'),
             'price_ru_retail' => $request->integer('price_ru_retail'),
             'price_ru_wholesale' => $request->integer('price_ru_wholesale'),
             'price_mng_retail' => $request->integer('price_mng_retail'),
             'price_mng_wholesale' => $request->integer('price_mng_wholesale'),
             'price_jp_retail' => $request->integer('price_jp_retail'),
             'price_jp_wholesale' => $request->integer('price_jp_wholesale'),
             'buying_price' => $request->integer('buying_price'),
             'selling_price' => $request->integer('selling_price'),
        ]);
        return response()->json([]);
    }

    public function updateStandardPrices(Request $request, NomenclatureBaseItemPdrCard $nomenclatureBaseItemPdrCard): JsonResponse
    {
        $nomenclatureBaseItemPdrCard->update([
            'price_nz_wholesale' => $request->integer('price_nz_wholesale'),
            'price_nz_retail' => $request->integer('price_nz_retail'),
            'price_ru_wholesale' => $request->integer('price_ru_wholesale'),
            'price_ru_retail' => $request->integer('price_ru_retail'),
            'price_jp_minimum_buy' => $request->integer('price_jp_minimum_buy'),
            'price_jp_maximum_buy' => $request->integer('price_jp_maximum_buy'),
            'price_jp_wholesale' => $request->integer('price_jp_wholesale'),
            'price_jp_retail' => $request->integer('price_jp_retail'),
            'price_mng_retail' => $request->integer('price_mng_retail'),
            'price_mng_wholesale' => $request->integer('price_mng_wholesale'),
            'nz_needs' => $request->integer('nz_needs'),
            'ru_needs' => $request->integer('ru_needs'),
            'mng_needs' => $request->integer('mng_needs'),
            'jp_needs' => $request->integer('jp_needs'),
            'pinnacle_price' => $request->integer('pinnacle_price'),
            'nz_team_price' => $request->integer('nz_team_price'),
            'nz_team_needs' => $request->integer('nz_team_needs'),
        ]);

        return response()->json([]);
    }
}
