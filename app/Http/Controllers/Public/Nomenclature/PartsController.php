<?php

namespace App\Http\Controllers\Public\Nomenclature;

use App\Actions\ReadyCars\ReadyCarsPartsListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableCars\PartApiResource;
use Illuminate\Http\Request;

class PartsController extends Controller
{
    public function list(Request $request, string $make, string $model): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $generation = $request->get('generation', '');
        $header = $request->get('header', '');
        $parts = app()->make(ReadyCarsPartsListAction::class)->handle($make, $model, $generation, $header);
        return PartApiResource::collection($parts);
    }
}
