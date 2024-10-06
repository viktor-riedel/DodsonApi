<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemCreateAction;
use App\Actions\BaseItem\BaseItemUpdatePartsList;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseItem\BaseItemGenerationResource;
use App\Http\Resources\BaseItem\BaseItemModelResource;
use App\Http\Resources\BaseItem\BaseItemResource;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseItemController extends Controller
{

    public function makes(): JsonResponse
    {
         $result = collect();
         $makes = array_unique(NomenclatureBaseItem::orderBy('make')
             ->where('make', '!=', '')
             ->whereNotNull('make')
             ->pluck('make')->toArray()
         );
         foreach($makes as $make) {
             $models = NomenclatureBaseItem::where('make', $make)
                 ->get();
             $result->push([
                 'make' => $make,
                 'models' => $models->groupBy('generation')->count(),
                 'preview_image' => $models->first()?->preview_image,
             ]);
         }
         return response()->json($result);
    }

    public function generations(string $make, string $model): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $generations = NomenclatureBaseItem::orderBy('generation')
            ->where(['make' => $make, 'model' => $model])
            ->groupBy('id', 'generation', 'preview_image')
            ->get(['id', 'generation', 'preview_image']);
        return BaseItemGenerationResource::collection($generations);
    }

    public function models(string $make): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $models = NomenclatureBaseItem::where(['make' => $make])
            ->orderBy('model')
            ->get();
        $filtered = collect(array_unique($models->pluck('model')->toArray()))
            ->transform(function($model) use ($models) {
                return [
                   'model' => $model,
                   'preview_image' => $models->where('model', $model)->first()->preview_image,
                   'generations' => $models->where('model', $model)->count(),
                ];
            });
        return BaseItemModelResource::collection($filtered);
    }

    public function save(Request $request): JsonResponse
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

    public function findByIcNumber(Request $request): JsonResponse
    {
        if ($request->query('search')) {
            $partName = $request->get('partName');
            $items = NomenclatureBaseItemPdrPosition::with('nomenclatureBaseItemPdr')
                ->when($partName, function($q) use ($partName) {
                    $q->whereHas('nomenclatureBaseItemPdr', function($query) use ($partName) {
                        $query->where('item_name_eng', $partName);
                    });
                })
                ->where('ic_number', 'like', $request->query('search') . '%')
                ->where('is_virtual', false)
                ->get();
            $ids = $items->pluck('id')->toArray();
            $reused = \DB::table('related_base_item_positions')
                    ->select('related_id')
                    ->whereIn('related_id', $ids)
                    ->get()
                    ->pluck('related_id')
                    ->toArray();
            $items = $items->filter(function($item) use ($reused) {
               return !in_array($item->id, $reused, true);
            })->values();
            return response()->json($items);
        }
        return response()->json([], 202);
    }

    public function baseItemUpdate(Request $request, NomenclatureBaseItem $baseItem): BaseItemResource
    {
        $baseItem->update($request->except('chassis', 'item_pdr', 'id', 'start_stop_dates'));
        $baseItem->load(['baseItemPDR' , 'baseItemPDR.nomenclatureBaseItemPdrCard']);
        return new BaseItemResource($baseItem);
    }

    public function baseItemDelete(NomenclatureBaseItem $baseItem): JsonResponse
    {
        $baseItem->update(['deleted_by' => null]);
        $baseItem->baseItemPDR()->delete();
        $baseItem->delete();
        return response()->json([], 202);
    }

    public function saveItemPdr(Request $request, NomenclatureBaseItem $baseItem): JsonResponse
    {
        app()->make(BaseItemUpdatePartsList::class)->handle($request->toArray(), $baseItem);
        return response()->json([], 202);
    }
}
