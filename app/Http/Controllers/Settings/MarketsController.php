<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\MarketResource;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $markets = Market::orderBy('name')->get();
        return MarketResource::collection($markets);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        Market::create($request->except('id'));
        return response()->json([], 201);
    }

    public function update(Request $request, Market $market): \Illuminate\Http\JsonResponse
    {
        $market->update($request->except('id'));
        return response()->json([], 202);
    }

    public function delete(Market $market)
    {
        $market->delete();
        return response()->json([], 202);
    }
}
