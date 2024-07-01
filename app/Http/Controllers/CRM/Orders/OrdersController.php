<?php

namespace App\Http\Controllers\CRM\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\Car\CarResource;
use App\Http\Resources\CRM\Orders\OrderResource;
use App\Http\Resources\CRM\Orders\ViewOrderResource;
use App\Http\Resources\Order\OrderItemResource;
use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $orders = Order::with('items', 'createdBy')
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return OrderResource::collection($orders);
    }

    public function statuses(): \Illuminate\Http\JsonResponse
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
    
    public function view(Request $request, Order $order): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getFullOrderData($order));
    }

    public function update(Request $request, Order $order): \Illuminate\Http\JsonResponse
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

        return [
            'car' => new CarResource($car),
            'order_items' => OrderItemResource::collection($order->items),
            'order' => new OrderResource($order)
        ];
    }
}
