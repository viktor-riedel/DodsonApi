<?php

namespace App\Http\Controllers\Cars;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCar\BaseCarResource;
use App\Models\BaseCar;
use App\Models\NomenclatureBaseItem;

class BaseCarsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $baseCars = BaseCar::with('nomenclatureBaseItem')
            ->paginate(10);

        return BaseCarResource::collection($baseCars);
    }

    public function makes(): \Illuminate\Http\JsonResponse
    {
        $makes = NomenclatureBaseItem::get()
            ->pluck('make')
            ->toArray();
        $makes = array_unique($makes);
        sort($makes);
        return response()->json($makes);
    }

    public function models(string $make): \Illuminate\Http\JsonResponse
    {
        $models = NomenclatureBaseItem::get()
            ->where('make', $make)
            ->pluck('model')
            ->toArray();
        $models = array_unique($models);
        sort($models);
        return response()->json($models);
    }
}
