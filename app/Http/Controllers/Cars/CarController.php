<?php

namespace App\Http\Controllers\Cars;

use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\CapartsApiHelper;

class CarController extends Controller
{
    public function importUnsoldCards(CapartsApiHelper $helper): \Illuminate\Http\JsonResponse
    {
        $cars =  $helper->importUnsoldCars();
        return response()->json($cars);
    }
}
