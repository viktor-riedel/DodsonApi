<?php

namespace App\Http\Controllers\Nomenclature;

use App\Http\Controllers\Controller;
use App\Models\NomenclatureBaseItemPdrCard;
use Illuminate\Http\Request;

class BaseItemPdrController extends Controller
{
    public function updateBasePdr(Request $request)
    {
        app()->make(\App\Actions\BaseItemPdrUpdateAction::class)->handle($request->all());
        return response()->json([], 202);
    }
}
