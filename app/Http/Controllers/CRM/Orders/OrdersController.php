<?php

namespace App\Http\Controllers\CRM\Orders;

use App\Events\Order\SyncCompleteOrderEvent;
use App\Exports\Excel\CreatedCarsOrdersExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Car\CarResource;
use App\Http\Resources\CRM\Orders\MakeResource;
use App\Http\Resources\CRM\Orders\ModelResource;
use App\Http\Resources\CRM\Orders\OrderResource;
use App\Http\Resources\CRM\Orders\OrderUserResource;
use App\Http\Resources\Order\OrderItemResource;
use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\Order;
use DB;
use Excel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrdersController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $userId = $request->get('userId');
        $make = $request->get('make');
        $model = $request->get('model');
        $search = $request->get('search');

        $carsIds = Car::where('make', $make)
            ->when($model, function ($query, $model) {
                return $query->where('model', $model);
            })
            ->get()
            ->pluck('id')
            ->toArray();

        $orders = Order::with('items', 'createdBy')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when($search, function ($query, $search) {
                return $query->where('order_number', 'like', '%' . $search . '%')
                    ->orWhereHas('items', function ($query) use ($search) {
                       return $query->whereHas('car', function ($query) use ($search) {
                           return $query->where('car_mvr', 'like', '%' . $search . '%')
                               ->orWhere('make', 'like', '%' . $search . '%')
                               ->orWhere('model', 'like', '%' . $search . '%')
                               ->orWhere('chassis', 'like', '%' . $search . '%');
                       });
                    });
            })
            ->when(count($carsIds), function ($query) use ($carsIds) {
                $query->whereHas('items', function ($query) use ($carsIds) {
                    $query->whereIn('car_id', $carsIds);
                });
            })
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        
        return OrderResource::collection($orders);
    }

    public function users(): AnonymousResourceCollection
    {
        $users = collect();
        Order::with('createdBy')
            ->get()
            ->pluck('createdBy')
            ->each(function($user) use (&$users) {
                if (!$users->where('id', $user->id)->first()) {
                    $users->push($user);
                }
            });

        return OrderUserResource::collection($users->sortBy('name'));
    }

    public function makes(): AnonymousResourceCollection
    {
        $makes = DB::table('cars')
            ->selectRaw('distinct(make)')
            ->whereRaw('id in (select car_id from order_items)')
            ->whereNull('cars.deleted_at')
            ->orderBy('make')
            ->get();
        return MakeResource::collection($makes);
    }

    public function models(string $make): AnonymousResourceCollection
    {
        $models = DB::table('cars')
            ->selectRaw('distinct(model)')
            ->whereRaw('id in (select car_id from order_items)')
            ->where('make', $make)
            ->whereNull('cars.deleted_at')
            ->orderBy('model')
            ->get();
        return ModelResource::collection($models);
    }

    public function statuses(): JsonResponse
    {
        $statuses = [];
        foreach(Order::ORDER_STATUS_STRING as $key => $value) {
            $statuses[] = [
                'id' => $key,
                'name' => $value,
            ];
        }

        return response()->json($statuses);
    }
    
    public function view(Request $request, Order $order): JsonResponse
    {
        return response()->json($this->getFullOrderData($order));
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $order->update([
            'order_status' => (int) $request->input('status')
        ]);
        $order->refresh();
        if ($order->order_status === Order::ORDER_STATUS_INT['CONFIRMED']) {
            if (config('app.env') !== 'production') {
                event(new SyncCompleteOrderEvent($order));
            }
        }
        return response()->json($this->getFullOrderData($order));
    }

    public function export(Request $request): JsonResponse
    {
        $userId = $request->get('userId');
        $orders = Order::with('items', 'createdBy')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->get();
        $filename = 'orders/export/orders_' . now()->toDateTimeString() . '.xlsx';
        Excel::store(new CreatedCarsOrdersExcelExport($orders), $filename, 's3', null, ['visibility' => 'public']);
        $url = \Storage::disk('s3')->url($filename);
        return response()->json(['link' => $url]);
    }

    private function getFullOrderData(Order $order): array
    {
        $order->refresh();
        $order->load('items', 'createdBy', 'latestSync');
        $car = $order->items->first()->car;
        if ($car) {
            $car->load('images', 'carAttributes', 'modifications');
        }

        // if parts order
        if ($order->items()->count()) {
            $order->items->each(function($item) {
                $item->pdr = CarPdrPosition::with('carPdr', 'carPdr.car')->find($item->part_id);
            });
        }

        return [
            'car' => $car ? new CarResource($car) : null,
            'order_items' => OrderItemResource::collection($order->items),
            'order' => new OrderResource($order),
        ];
    }
}
