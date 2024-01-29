<?php

namespace App\Http\Controllers\Public\Nomenclature;

use App\Actions\ReadyCars\ReadyCarsModificationsAction;
use App\Http\Controllers\Controller;

class ModificationsController extends Controller
{
    public function list(string $make, string $model, string $generation): \Illuminate\Http\JsonResponse
    {
        $modifications = app()->make(ReadyCarsModificationsAction::class)->handle($make, $model, $generation);
        return response()->json($modifications);
    }
}
