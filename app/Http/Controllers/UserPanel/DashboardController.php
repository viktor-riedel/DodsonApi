<?php

namespace App\Http\Controllers\UserPanel;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function list(): \Illuminate\Http\JsonResponse
    {
        $orders = 0;
        $favourites = 0;
        $invoices = 0;
        $cart = 0;
        $cars = 0;
        $parts = 0;

        return response()->json([
            'orders' => $orders,
            'favourites' => $favourites,
            'invoices' => $invoices,
            'cart' => $cart,
            'cars' => $cars,
            'parts' => $parts,
        ]);
    }
}
