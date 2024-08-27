<?php

namespace App\Http\Controllers\CRM\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\Car\CarResource;
use App\Http\Resources\CRM\Orders\OrderResource;
use App\Http\Resources\CRM\Orders\OrderUserResource;
use App\Http\Resources\CRM\Orders\ViewOrderResource;
use App\Http\Resources\Order\OrderItemResource;
use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrdersController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $userId = $request->get('userId');
        $orders = Order::with('items', 'createdBy')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
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
        $order->update(['order_status' => (int) $request->input('status')]);
        return response()->json($this->getFullOrderData($order));
    }

    private function getFullOrderData(Order $order): array
    {
        $order->refresh();
        $order->load('items', 'createdBy');
        $car = $order->items->first()->car;
        if ($car) {
            $car->load('images', 'carAttributes', 'modifications');
        }

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
