<?php

namespace App\Http\Controllers\SellingPartsMap;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellingPartsMap\DefaultItemResource;
use App\Http\Resources\SellingPartsMap\SellingMapItemResource;
use App\Http\Traits\DefaultSellingMapTrait;
use App\Models\SellingMapItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellingPartsMapController extends Controller
{
    use DefaultSellingMapTrait;

    public const MAIN_DIRECTORIES = [
        'Engine and Transmission',
        'Frontend Assy (Nosecut)',
        'Body Exterior',
        'Interior',
        'Front Suspension',
        'Rear Suspension',
        'Other Parts',
    ];

    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->createMainFolders(self::MAIN_DIRECTORIES);
        $map = $this->getDefaultSellingMap();
        return SellingMapItemResource::collection($map);
    }

    public function partsList(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return DefaultItemResource::collection($this->getDefaultPartsListWithoutUsed());
    }

    public function addPartToGroup(Request $request, SellingMapItem $item): JsonResponse
    {
        SellingMapItem::create([
            'parent_id' => $item->id,
            'item_name_eng' => $request->input('item_name_eng'),
            'item_name_ru' => $request->input('item_name_ru'),
            'price_jpy' => $request->integer('price'),
        ]);

        return response()->json($this->getDefaultMaps());
    }

    public function addPartsToGroup(Request $request, SellingMapItem $item): JsonResponse
    {
        if (is_array($request->input('parts'))) {
            foreach($request->input('parts') as $part) {
                SellingMapItem::create([
                    'parent_id' => $item->id,
                    'item_name_eng' => $part['item_name_eng'],
                    'item_name_ru' => $part['item_name_ru'],
                ]);
            }
        }
        return response()->json($this->getDefaultMaps());
    }

    public function deletePart(SellingMapItem $item): JsonResponse
    {
        $item->delete();
        return response()->json($this->getDefaultMaps());
    }

    public function updatePartPrice(Request $request, SellingMapItem $item): JsonResponse
    {
        $item->update([
            'price_jpy' => $request->input('price'),
        ]);
        return response()->json([], 202);
    }

    private function getDefaultMaps(): array
    {
        return [
            'map' => SellingMapItemResource::collection($this->getDefaultSellingMap()),
            'default_parts' => DefaultItemResource::collection($this->getDefaultPartsListWithoutUsed()),
        ];
    }

}
