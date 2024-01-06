<?php

namespace App\Http\Controllers\ReadyCars;

use App\Http\Controllers\Controller;
use App\Models\NomenclatureBaseItem;

class ReadyCarsController extends Controller
{
    public function list(): \Illuminate\Http\JsonResponse
    {
        $result = [];
        $positions = NomenclatureBaseItem::with('NomenclaturePositionsNotVirtual')
            ->get()
            ->filter(function($item) {
                return count($item->NomenclaturePositionsNotVirtual);
            });
        $makes = array_unique($positions->pluck('make')->toArray());
        foreach($makes as $make) {
             $result[] = [
                 'make' => $make,
                 'models' => $positions->where('make', $make)->count(),
                 'preview_image' => $positions->where('make', $make)->first()->preview_image
             ];
        }
        return response()->json($result);

    }
}
