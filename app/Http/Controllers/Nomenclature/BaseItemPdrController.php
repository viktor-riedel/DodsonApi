<?php

namespace App\Http\Controllers\Nomenclature;

use App\Http\Controllers\Controller;
use App\Models\NomenclatureBaseItemPdrCard;
use Illuminate\Http\Request;

class BaseItemPdrController extends Controller
{
    public function update(Request $request, NomenclatureBaseItemPdrCard $pdrCard): \Illuminate\Http\JsonResponse
    {
        $pdrCard->nomenclatureBaseItemPdr()->update([
            'item_name_eng' => $request->input('name_eng'),
            'item_name_ru' => $request->input('name_ru'),
        ]);
        $pdrCard->update($request->except(['id', 'nomenclature_base_item_pdr_id']));
        return response()->json([], 202);
    }

    public function updateBasePdr(Request $request)
    {
        app()->make(\App\Actions\BaseItemPdrUpdateAction::class)->handle($request->all());
        return response()->json([], 202);
    }
}
