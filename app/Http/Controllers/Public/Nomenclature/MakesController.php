<?php

namespace App\Http\Controllers\Public\Nomenclature;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\MakesResource;
use App\Models\NomenclatureBaseItem;

class MakesController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $makes = NomenclatureBaseItem::query()->distinct()->orderBy('make')->get('make');
        return MakesResource::collection($makes);
    }
}
