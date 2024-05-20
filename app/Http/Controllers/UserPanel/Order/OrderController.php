<?php

namespace App\Http\Controllers\UserPanel\Order;

use App\Actions\UserPanel\CreateOrderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function list(Request $request)
    {

    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $orderId = app()->make(CreateOrderAction::class)->handle($request);
        return response()->json(['id' => $orderId]);
    }
}
