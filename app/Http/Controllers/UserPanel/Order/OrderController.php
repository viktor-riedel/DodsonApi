<?php

namespace App\Http\Controllers\UserPanel\Order;

use App\Actions\UserPanel\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $orders = $request->user()->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return OrderResource::collection($orders);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $orderId = app()->make(CreateOrderAction::class)->handle($request);
        return response()->json(['id' => $orderId]);
    }
}
