<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemCreateAction;
use App\Actions\BaseItem\BaseItemUpdatePartsList;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseItem\BaseItemResource;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class BaseItemController extends Controller
{

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $query = NomenclatureBaseItem::query()->with(['baseItemPDR']);
        if ($request->query('make')) {
            $query->where('make', $request->query('make'));
        }
        if ($request->query('model')) {
            $query->where('model', $request->query('model'));
        }
        if ($request->query('generation')) {
            $query->where('generation', $request->query('generation'));
        }
        if ($request->query('header')) {
            $query->where('header', $request->query('header'));
        }
        $baseItems = $query->paginate(10);
        return BaseItemResource::collection($baseItems);
    }

    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        $newBaseItemId = app()->make(BaseItemCreateAction::class)->handle($request);
        return response()->json(['id' =>  $newBaseItemId], 201);
    }


    public function edit(NomenclatureBaseItem $baseItem): BaseItemResource
    {
        $baseItem->load([
            'baseItemPDR',
            'baseItemPDR.nomenclatureBaseItemVirtualPosition.photos'
        ]);
        return new BaseItemResource($baseItem);
    }

    public function baseItemUpdate(Request $request, NomenclatureBaseItem $baseItem): BaseItemResource
    {
        $baseItem->update($request->except('chassis', 'item_pdr', 'id', 'start_stop_dates'));
        $baseItem->load(['baseItemPDR' , 'baseItemPDR.nomenclatureBaseItemPdrCard']);
        return new BaseItemResource($baseItem);
    }

    public function baseItemDelete(NomenclatureBaseItem $baseItem): \Illuminate\Http\JsonResponse
    {
        $baseItem->update(['deleted_by' => null]);
        $baseItem->baseItemPDR()->delete();
        $baseItem->delete();
        return response()->json([], 202);
    }

    public function saveItemPdr(Request $request, NomenclatureBaseItem $baseItem): \Illuminate\Http\JsonResponse
    {
        app()->make(BaseItemUpdatePartsList::class)->handle($request->toArray(), $baseItem);
        return response()->json([], 202);
    }
}
