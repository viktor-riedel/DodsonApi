<?php

namespace App\Http\Controllers\Nomenclature;

use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CatalogApiHelper;

class NomenclatureController extends Controller
{

    private CatalogApiHelper $apiHelper;

    public function __construct()
    {
        $this->apiHelper = new CatalogApiHelper();
    }
    
    // api calls
    public function getCatalogMakes(): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiHelper->loadMakes();
        return response()->json($data);
    }

    public function getCatalogModels(int $make): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiHelper->loadModels($make);
        return response()->json($data);
    }

    public function getCatalogGenerations(int $model): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiHelper->loadGenerations($model);
        return response()->json($data);
    }

    public function getCatalogMvrsHeaders(int $make, int $model): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiHelper->loadMvrHeaders($make, $model);
        return response()->json($data);
    }

    public function getCatalogPdr(int $mvrId): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiHelper->loadPdr($mvrId);
        return response()->json($data);
    }
}
