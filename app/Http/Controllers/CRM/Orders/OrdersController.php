<?php

namespace App\Http\Controllers\CRM\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\CRM\Orders\OrderResource;
use App\Http\Resources\CRM\Orders\ViewOrderResource;
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
    
    public function view(Request $request, Order $order): ViewOrderResource
    {
        $order->load('carItems', 'createdBy');
        $carIds = $order->carItems->pluck('id')->toArray();
        $order->cars = Car::with('images', 'carAttributes', 'modifications', 'positions',
                'carFinance', 'positions.card', 'positions.card.priceCard',
                'positions.card.partAttributesCard', 'positions.card.images')
                ->whereIn('id', $carIds)
                ->get();

        return new ViewOrderResource($order);
    }
}
