<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeftoversController extends Controller
{
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        \Log::debug(json_encode($request->all()));
        return response()->json(['ok' => true]);
    }
}
