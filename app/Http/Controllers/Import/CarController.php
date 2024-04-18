<?php

namespace App\Http\Controllers\Import;

use App\Actions\Import\ImportFromCapartsAction;
use App\Http\Controllers\Contracts\Importable;
use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CapartsApiHelper;
use Illuminate\Http\Request;

class CarController extends Controller implements Importable
{
    public function importResources(): \Illuminate\Http\JsonResponse
    {
        $helper = new CapartsApiHelper();
        $cars = $helper->importUnsoldCars();
        //TO DO remove already imported cars from the collection
        return response()->json($cars);
    }

    public function importEntity(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = app()->make(ImportFromCapartsAction::class)->handle($request, $request->user()->id);
        return response()->json(['id' => $result]);
    }
}
