<?php

namespace App\Http\Controllers\Public;

use App\Actions\UpdatePrices\UpdatePricesAction;
use App\Http\Controllers\Controller;
use App\Models\CatalogUpdateLog;
use Illuminate\Http\Request;

class LeftoversController extends Controller
{
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        CatalogUpdateLog::create([
            'ip_address' => $request->ip(),
            'agent' => $request->userAgent(),
            'api_point' => '/stats/update',
            'user_id' => $request->user()->id,
            'packet' => json_encode($request->all()),
        ]);
        # only for 1c update
        if ($request->all() !== []) {

        }
        $result = app()->make(UpdatePricesAction::class)->handle($request->all());
        return response()->json($result);
    }
}
