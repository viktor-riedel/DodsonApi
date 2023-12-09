<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemPdrUpdateAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseItemPdrController extends Controller
{
    public function updateBasePdr(Request $request)
    {
        app()->make(BaseItemPdrUpdateAction::class)->handle($request->all());
        return response()->json([], 202);
    }
}
