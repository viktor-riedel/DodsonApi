<?php

namespace App\Http\Controllers\Cars;

use App\Actions\BaseCar\CreateBaseCarAction;
use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Resources\BaseCar\BaseCarFullResource;
use App\Http\Resources\BaseCar\BaseCarResource;
use App\Models\BaseCar;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;

class BaseCarsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $baseCars = BaseCar::with('nomenclatureBaseItem')
            ->paginate(10);

        return BaseCarResource::collection($baseCars);
    }

    public function find(BaseCar $baseCar): BaseCarFullResource
    {
        $baseCar->load('nomenclatureBaseItem', 'nomenclatureBaseItem.baseItemPDR', 'nomenclatureBaseItem.baseItemPDR.nomenclatureBaseItemPdrPositions');
        return new BaseCarFullResource($baseCar);
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
        $models = \DB::table('nomenclature_base_items')
                ->select(['model', 'generation', 'restyle'])
                ->distinct()
                ->where('make', $make)
                ->orderBy('make')
                ->get();
        $models = $models->transform(function($item) {
           return [
                'model' => $item->model,
                'generation' => $item->generation,
                'restyle' => $item->restyle,
                'title' => $item->model . ' GEN ' . $item->generation . ($item->restyle ? ' RESTYLE' : ''),
           ];
        });
        return response()->json($models);
    }

    public function getHeaders(Request $request, string $make, string $model, string $generation, CatalogApiHelper $helper): \Illuminate\Http\JsonResponse
    {
        $restyle = $request->get('restyle');
        if ($restyle) {
            $headers = $helper->findMvrHeadersByMakeModelGeneration($make, $model, $generation, true);
        } else {
            $headers = $helper->findMvrHeadersByMakeModelGeneration($make, $model, $generation);
        }
        return response()->json($headers);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = app()->make(CreateBaseCarAction::class)->handle($request);
        return response()->json(['id' => $id], 201);
    }

    public function delete(BaseCar $baseCar)
    {
        $baseCar->delete();
        return response()->json(['message' => 'deleted'], 202);
    }

    public function update(Request $request, BaseCar $baseCar): \Illuminate\Http\JsonResponse
    {
        $baseCar->update([
            'make' => mb_strtoupper($request->input('make')),
            'model' => mb_strtoupper($request->input('model')),
            'generation' => mb_strtoupper($request->input('generation')),
            'generation_number' => (int) $request->input('generation_number'),
            'body_type' => mb_strtoupper($request->input('body_type')),
            'doors' => (int) $request->input('doors'),
            'month_start' => (int) $request->input('month_start'),
            'month_stop' => (int) $request->input('month_stop'),
            'year_start' => (int) $request->input('year_start'),
            'year_stop' => (int) $request->input('year_stop'),
            'restyle' => $request->input('restyle') ?? false,
            'not_restyle' => $request->input('not_restyle') ?? false,
        ]);
        return response()->json(['message' => 'updated'], 202);
    }
}
