<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItemCreateAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseItem\BaseItemResource;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class BaseItemController extends Controller
{

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $baseItems = NomenclatureBaseItem::with(['baseItemPDR' , 'baseItemPDR.nomenclatureBaseItemPdrCard'])
            ->paginate(10);

        return BaseItemResource::collection($baseItems);
    }

    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        app()->make(BaseItemCreateAction::class)->handle($request);
        return response()->json(['base item created' =>  true], 201);
    }


    public function edit(NomenclatureBaseItem $baseItem): BaseItemResource
    {
        $baseItem->load(['baseItemPDR' , 'baseItemPDR.nomenclatureBaseItemPdrCard']);
        return new BaseItemResource($baseItem);
    }

    public function baseItemUpdate(Request $request, NomenclatureBaseItem $baseItem): BaseItemResource
    {
        $baseItem->update($request->except('chassis', 'item_pdr', 'id', 'start_stop_dates'));
        $baseItem->load(['baseItemPDR' , 'baseItemPDR.nomenclatureBaseItemPdrCard']);
        return new BaseItemResource($baseItem);
    }
}
