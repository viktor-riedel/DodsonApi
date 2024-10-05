<?php

namespace App\Http\Controllers\AllCars;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableCars\GenerationResource;
use App\Http\Resources\AvailableCars\MakeResource;
use App\Http\Resources\AvailableCars\ModelResource;
use App\Http\Resources\Car\CarResource;
use App\Http\Resources\Car\ContrAgentResource;
use App\Http\Resources\Car\CreatedByResource;
use App\Models\Car;
use App\Models\CarFinance;
use App\Models\CarPdrPositionCardPrice;
use App\Models\SyncData;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AllCarsController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make');
        $model = $request->get('model');
        $generation = $request->get('generation');
        $car_status = $request->get('status', -1);
        $user = $request->get('user', -1);
        $agent = $request->get('agent');
        $text = $request->get('search');
        $dateStart = null;
        $dateStop = null;
        $perPage = $request->get('perPage', 30);

        $sortByUser = $request->get('sortByUser');
        $sortByMvr = $request->get('sortByMvr');
        $sortByDate = $request->get('sortByDate');
        $sortByBuyingPrice = $request->get('sortByBuyingPrice');
        $sortBySellingPrice = $request->get('sortBySellingPrice');
        $sortByContrAgent = $request->get('sortByContrAgent');
        $sortBySellingStatus = $request->get('sortBySellingStatus');
        $sortBySync = $request->get('sortBySync');
        $sortByMake = $request->get('sortByMake');
        $sortByModel = $request->get('sortByModel');
        $sortByStatus = $request->get('sortByStatus');

        if ($request->get('dateStart')) {
            try {
                $dateStart = Carbon::createFromFormat('d/m/Y', $request->get('dateStart'))->format('Y-m-d');
            } catch (\Exception $e) {
                //
            }
        }
        if ($request->get('dateStop')) {
            try {
                $dateStop = Carbon::createFromFormat('d/m/Y', $request->get('dateStop'))->format('Y-m-d');
            } catch (\Exception $e) {
                //
            }
        }

        $cars = Car::with(['images', 'carAttributes', 'carFinance',
            'modification', 'positions', 'positions.card', 'latestSyncData',
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
            ->when($car_status !== null && $car_status >= 0, function ($query) use ($car_status) {
                return $query->where('car_status', $car_status);
            })
            ->when($agent, function($query) use ($agent) {
                return $query->where('contr_agent_name', $agent);
            })
            ->when($user, function ($query) use ($user) {
                return $query->where('created_by', $user);
            })
            ->when($dateStart, function ($query) use ($dateStart) {
                return $query->where('created_at', '>=', $dateStart);
            })
            ->when($dateStop, function ($query) use ($dateStop) {
                return $query->where('created_at', '<=', $dateStop);
            })
            ->where(function ($query) use ($text) {
                return $query->when($text, function ($query) use ($text) {
                    return $query->where('make', 'like', "%$text%")
                        ->orWhere('model', 'like', "%$text%")
                        ->orWhere('chassis', 'like', "%$text%")
                        ->orWhere('contr_agent_name', 'like', "%$text%")
                        ->orWhere('car_mvr', 'like', "%$text%");
                });
            })
            ->where('virtual', false)
            ->when($sortByUser, function ($query) use ($sortByUser) {
                return $query->orderBy('created_by', $sortByUser);
            })
            ->when($sortByMvr, function ($query) use ($sortByMvr) {
                return $query->orderBy('car_mvr', $sortByMvr);
            })
            ->when($sortByDate, function ($query) use ($sortByDate) {
                return $query->orderBy('created_at', $sortByDate);
            })
            ->when($sortByContrAgent, function ($query) use ($sortByContrAgent) {
                return $query->orderBy('contr_agent_name', $sortByContrAgent);
            })
            ->when($sortBySellingStatus, function ($query) use ($sortBySellingStatus) {
                return $query->orderBy(CarFinance::select('car_is_for_sale')
                    ->whereColumn('car_id', 'cars.id'), $sortBySellingStatus);
            })
            ->when($sortByBuyingPrice, function ($query) use ($sortByBuyingPrice) {
                return $query->orderBy(CarFinance::select('purchase_price')
                    ->whereColumn('car_id', 'cars.id'), $sortByBuyingPrice);
            })
            ->when($sortBySellingPrice, function ($query) use ($sortBySellingPrice) {
                return $query->orderBy(CarFinance::select('selling_price')
                    ->whereColumn('car_id', 'cars.id'), $sortBySellingPrice);
            })
            ->when($sortBySync, function ($query) use ($sortBySync) {
                return $query->orderBy(SyncData::select('document_number')
                    ->whereColumn('syncable_id', 'cars.id')
                    ->orderBy('created_at', 'desc')
                    ->take(1), $sortBySync);
            })
            ->when($sortByMake, function ($query) use ($sortByMake) {
                return $query->orderBy('make', $sortByMake);
            })
            ->when($sortByModel, function ($query) use ($sortByModel) {
                return $query->orderBy('make', $sortByModel);
            })
            ->when($sortByStatus, function ($query) use ($sortByStatus) {
                return $query->orderBy('car_status', $sortByStatus);
            })
            ->when(!$sortByUser &&
                !$sortByMvr &&
                !$sortByDate &&
                !$sortByBuyingPrice &&
                !$sortBySellingPrice &&
                !$sortByContrAgent &&
                !$sortBySellingStatus &&
                !$sortBySync &&
                !$sortByMake &&
                !$sortByModel, function($query) {
                return $query->orderBy('created_at', 'desc');
            })
            ->paginate($perPage);

        $cars->getCollection()->each(function ($car) {
            $car->parts_price = (int) $car->carFinance->purchase_price === 0 ?
                $car->positions->sum('card.priceCard.selling_price') :
                $car->carFinance->purchase_price;
            $car->selling_price = $car->positions->sum('card.priceCard.buying_price');
        });

        return CarResource::collection($cars);
    }

    public function currencyList(): JsonResponse
    {
        return response()->json(CarPdrPositionCardPrice::getCurrenciesJson());
    }

    public function makes(): AnonymousResourceCollection
    {
        $makes = Car::orderBy('make')
            ->where('virtual', false)
            ->get()->pluck('make')->unique();
        return MakeResource::collection($makes);
    }

    public function models(string $make): AnonymousResourceCollection
    {
        $models = Car::where('make', $make)
            ->where('virtual', false)
            ->orderBy('model')
            ->get()->pluck('model')->unique();
        return ModelResource::collection($models);
    }

    public function generations(string $make, string $model): AnonymousResourceCollection
    {
        $generations = Car::where('make', $make)
            ->where('virtual', false)
            ->where('model', $model)
            ->orderBy('generation')
            ->get()->pluck('generation')->unique();
        return GenerationResource::collection($generations);
    }

    public function statusList(): JsonResponse
    {
        return response()->json(['status' => Car::getStatusesJson()]);
    }

    public function usersList(): AnonymousResourceCollection
    {
        $users = Car::with('createdBy')
            ->where('virtual', false)
            ->orderBy('created_by')
            ->get()
            ->pluck('createdBy')
            ->unique();
        return CreatedByResource::collection($users);
    }

    public function agentsList(): AnonymousResourceCollection
    {
        $agents = Car::where('virtual', false)
            ->whereNotNull('contr_agent_name')
            ->where('contr_agent_name', '!=', '')
            ->orderBy('contr_agent_name')
            ->get()
            ->pluck('contr_agent_name')
            ->unique()
            ->flatten()
            ->transform(function ($item) {
                return ['name' => $item];
            });
        return ContrAgentResource::collection($agents);
    }
}
