<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\PartsListDefaultAction;
use App\Http\Controllers\Controller;

class PartsListController extends Controller
{
    public function getDefaultPartsList()
    {
        $list = app()->make(PartsListDefaultAction::class)->handle();
        return response()->json($list);
    }
}
