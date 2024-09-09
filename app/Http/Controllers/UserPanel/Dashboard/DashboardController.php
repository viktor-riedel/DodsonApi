<?php

namespace App\Http\Controllers\UserPanel\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\CartTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CartTrait;

    public function list(Request $request): \Illuminate\Http\JsonResponse
    {

        $this->checkCartExist($request->user());

        $orders = $request->user()->orders->count();
        $favourites = $request->user()->wishListItems->count();
        $balance = number_format($request->user()->balance->sum('closing_balance'));
        $invoices = 0;
        $cart = $request->user()->cart->cartItems->count();
        $cars = 0;
        $parts = 0;

        return response()->json([
            'orders' => $orders,
            'favourites' => $favourites,
            'invoices' => $invoices,
            'balance' => $balance,
            'cart' => $cart,
            'cars' => $cars,
            'parts' => $parts,
        ]);
    }
}
