<?php

namespace App\Http\Controllers\Public\Nomenclature;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseItemPdrPositionCardApiResource;
use App\Models\NomenclatureBaseItemPdrCard;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    public function index(string $id)
    {
        $card = NomenclatureBaseItemPdrCard::where('inner_id', $id)->withTrashed()->first();
        if (!$card) {
            return response()->json('not found', 404);
        }

        return new BaseItemPdrPositionCardApiResource($card);
    }

    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $card = NomenclatureBaseItemPdrCard::where('inner_id', $id)->withTrashed()->first();
        if (!$card) {
            return response()->json('not found', 404);
        }
        //update card
        return response()->json('success', 202);
    }
}
