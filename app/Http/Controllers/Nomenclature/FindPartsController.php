<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\FindPart\FindPartAction;
use App\Actions\FindPart\FindPartGenerationsAction;
use App\Actions\FindPart\FindPartMakesAction;
use App\Actions\FindPart\FindPartModelsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\FindPart\FindPartResource;
use Illuminate\Http\Request;

class FindPartsController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $make = $request->get('make', '');
        $model = $request->get('model', '');
        $generation = $request->get('generation', '');
        $list = app()->make(FindPartAction::class)->handle($page, $search, $make, $model, $generation);
        return FindPartResource::collection($list);
    }

    public function makes(): \Illuminate\Http\JsonResponse
    {
        $makes = app()->make(FindPartMakesAction::class)->handle();
        return response()->json($makes);
    }

    public function models(string $make): \Illuminate\Http\JsonResponse
    {
        $models = app()->make(FindPartModelsAction::class)->handle($make);
        return response()->json($models);
    }

    public function generations(string $make, string $model): \Illuminate\Http\JsonResponse
    {
        $models = app()->make(FindPartGenerationsAction::class)->handle($make, $model);
        return response()->json($models);
    }
}
