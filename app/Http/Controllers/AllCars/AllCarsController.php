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
use App\Models\CarPdrPositionCardPrice;
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
            ->when($car_status, function ($query) use ($car_status) {
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
                        ->orWhere('car_mvr', 'like', "%$text%");
                });
            })
            ->where('virtual', false)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $cars->getCollection()->each(function ($car) {
            $car->parts_price =  (int) $car->carFinance->purchase_price === 0 ?
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
