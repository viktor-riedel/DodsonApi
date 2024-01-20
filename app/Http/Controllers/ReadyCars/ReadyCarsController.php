<?php

namespace App\Http\Controllers\ReadyCars;

use App\Http\Controllers\Controller;
use App\Models\NomenclatureBaseItem;

class ReadyCarsController extends Controller
{
    public function list(): \Illuminate\Http\JsonResponse
    {
        $result = [];
        $positions = NomenclatureBaseItem::with('NomenclaturePositionsNotVirtual')
            ->get()
            ->filter(function($item) {
                return count($item->NomenclaturePositionsNotVirtual);
            });
        $makes = array_unique($positions->pluck('make')->toArray());
        foreach($makes as $make) {
             $result[] = [
                 'make' => $make,
                 'models' => $positions->where('make', $make)->count(),
                 'preview_image' => $positions->where('make', $make)->first()->preview_image
             ];
        }
        return response()->json($result);
    }

    public function models(string $make): \Illuminate\Http\JsonResponse
    {
        $result = [];
        $positions = NomenclatureBaseItem::with('NomenclaturePositionsNotVirtual')
            ->where('make', $make)
            ->get()
            ->filter(function($item) {
                return count($item->NomenclaturePositionsNotVirtual);
            });
        $models = array_unique($positions->pluck('model')->toArray());
        foreach ($models as $model) {
            $result[] = [
                'model' => $model,
                'generations' => $positions->where('make', $make)->where('model', $model)->count(),
                'preview_image' => $positions->where('make', $make)->first()->preview_image
            ];
        }
        return response()->json($result);
    }


    public function generations(string $make, string $models)
    {
        $result = [];
        $positions = NomenclatureBaseItem::with('NomenclaturePositionsNotVirtual')
            ->where('make', $make)
            ->where('model', $models)
            ->get()
            ->filter(function($item) {
                return count($item->NomenclaturePositionsNotVirtual);
            });
        $generations = array_unique($positions->pluck('generation')->toArray());
        foreach ($generations as $generation) {
            $result[] = [
                'generation' => $generation,
                'preview_image' => $positions->where('make', $make)->first()->preview_image
            ];
        }
        return response()->json($result);
    }
}
