<?php

namespace App\Http\Controllers\UserPanel\Order;

use App\Actions\Order\CreateCarOrderAction;
use App\Actions\UserPanel\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\UserOrderResource;
use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return OrderResource::collection($orders);
    }

    public function order(Request $request, Order $order): UserOrderResource
    {
        $userOrder = $request->user()->orders()
                ->where('id', $order->id)
                ->with('items')
                ->first();
        if (!$userOrder) {
            abort(404, 'Order not found');
        }
        return new UserOrderResource($userOrder);
    }

    public function create(Request $request): JsonResponse
    {
        $orderId = app()->make(CreateOrderAction::class)->handle($request);
        return response()->json(['id' => $orderId]);
    }

    public function createCarOrder(Request $request, Car $car): JsonResponse
    {
        //check if car is already ordered
        if (!$car->has_active_order) {
            app()->make(CreateCarOrderAction::class)->handle($request, $car);
            return response()->json([], 201);
        }
        return response()->json(['error' => 'Car is not available'], 413);
    }
}
