<?php

namespace App\Http\Controllers\ReadyCars;

use App\Actions\ReadyCars\ReadyCarsModificationsAction;
use App\Actions\ReadyCars\ReadyCarsPartsListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableCars\PartResource;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

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

    public function models(string $make, ReadyCarsPartsListAction $action): \Illuminate\Http\JsonResponse
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
                'parts_count' => $action->handle($make, $model)->count(),
                'model' => $model,
                'generations' => $positions->where('make', $make)->where('model', $model)->count(),
                'preview_image' => $positions->where('make', $make)->first()->preview_image
            ];
        }
        return response()->json($result);
    }


    public function generations(string $make, string $model,
            ReadyCarsPartsListAction $action,
            ReadyCarsModificationsAction $modsAction): \Illuminate\Http\JsonResponse
    {
        $result = [];
        $positions = NomenclatureBaseItem::with('NomenclaturePositionsNotVirtual')
            ->where('make', $make)
            ->where('model', $model)
            ->get()
            ->filter(function($item) {
                return count($item->NomenclaturePositionsNotVirtual);
            });
        $generations = array_unique($positions->pluck('generation')->toArray());
        foreach ($generations as $generation) {
            $result[] = [
                'parts_count' => $action->handle($make, $model, $generation)->count(),
                'modifications_count' => $modsAction->handle($make, $model, $generation)->count(),
                'generation' => $generation,
                'preview_image' => $positions->where('make', $make)->first()->preview_image
            ];
        }
        return response()->json($result);
    }

    public function modifications(string $make, string $model, string $generation): \Illuminate\Http\JsonResponse
    {
        $modifications = app()->make(ReadyCarsModificationsAction::class)->handle($make, $model, $generation);
        return response()->json($modifications);
    }


    public function partsList(Request $request, string $make, string $model): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $generation = $request->get('generation', '');
        $header = $request->get('header', '');
        $parts = app()->make(ReadyCarsPartsListAction::class)->handle($make, $model, $generation, $header);
        return PartResource::collection($parts);
    }
}
