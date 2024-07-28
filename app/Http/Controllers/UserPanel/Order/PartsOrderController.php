<?php

namespace App\Http\Controllers\UserPanel\Order;

use App\Actions\Order\AddPartsToOrderAction;
use App\Actions\Order\CreatePartsOrderAction;
use App\Actions\Order\DeletePartsFromOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Part\WholesalePartResource;
use App\Models\CarPdrPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PartsOrderController extends Controller
{
    public function addToCart(Request $request): AnonymousResourceCollection
    {
        $parts = app()->make(AddPartsToOrderAction::class)->handle($request);
        return WholesalePartResource::collection($parts);
    }

    public function cart(Request $request): AnonymousResourceCollection
    {
        $parts = CarPdrPosition::with('carPdr', 'carPdr.car',
            'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard')
            ->whereIn('id', $request->user()->cart->partItems->pluck('part_id')->toArray())
            ->get();
        return WholesalePartResource::collection($parts);
    }

    public function updateCart(Request $request): AnonymousResourceCollection
    {
        $parts = app()->make(DeletePartsFromOrderAction::class)->handle($request);
        return WholesalePartResource::collection($parts);
    }

    public function makePartsOrder(Request $request): JsonResponse
    {
        $result = app()->make(CreatePartsOrderAction::class)->handle($request);
        return response()->json(['created' => $result]);
    }
}
