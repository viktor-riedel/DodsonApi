<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\FindPart\FindPartAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\FindPart\FindPartResource;
use Illuminate\Http\Request;

class FindPartsController extends Controller
{
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $list = app()->make(FindPartAction::class)->handle($page, $search);
        return FindPartResource::collection($list);
    }
}
