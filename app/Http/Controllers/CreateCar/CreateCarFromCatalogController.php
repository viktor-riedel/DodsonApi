<?php

namespace App\Http\Controllers\CreateCar;

use App\Actions\CreateCar\CreateNewCarFromCatalogAction;
use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CatalogApiHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateCarFromCatalogController extends Controller
{

    private CatalogApiHelper $catalogApiHelper;

    public function __construct()
    {
        $this->catalogApiHelper = new CatalogApiHelper();
    }

    public function makes(): JsonResponse
    {
        $makes = $this->catalogApiHelper->loadMakes();
        return response()->json($makes);
    }

    public function models(int $make): JsonResponse
    {
        $makes = $this->catalogApiHelper->loadModels($make);
        return response()->json($makes);
    }

    public function generations(int $model): JsonResponse
    {
        $generations = $this->catalogApiHelper->loadGenerations($model);
        return response()->json($generations);
    }

    public function modifications(int $model): JsonResponse
    {
        $modifications = $this->catalogApiHelper->modificationsByGeneration($model);
        return response()->json($modifications);
    }

    public function createNewCar(Request $request): JsonResponse
    {
        $id = app()->make(CreateNewCarFromCatalogAction::class)->handle($request->all(), $request->user()->id);
        return response()->json(['id' => $id]);
    }

    public function modificationsByGeneration(string $model_id, string $generation): JsonResponse
    {
        $generations = $this->catalogApiHelper->modificationsByGeneration((int) $model_id, $generation);
        return response()->json($generations);
    }
}
