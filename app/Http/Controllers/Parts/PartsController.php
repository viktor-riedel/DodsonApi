<?php

namespace App\Http\Controllers\Parts;

use App\Actions\Import\ImportFromPinnacleCsvAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartsController extends Controller
{
    public function list()
    {
        return response()->json([]);
    }

    public function importFromPinnacle(Request $request): JsonResponse
    {
        if ($request->file('uploadPartsPinnacle')) {
            app()->make(ImportFromPinnacleCsvAction::class)->handle($request);
        }
        return response()->json(['loaded' => 100]);
    }

    public function importFromOneC(Request $request)
    {
        return response()->json([]);
    }
}
