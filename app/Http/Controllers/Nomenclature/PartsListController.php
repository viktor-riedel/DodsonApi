<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\PartsList\AddNewPartsListAction;
use App\Actions\PartsList\CreateNewPartListAction;
use App\Actions\PartsList\DeletePartsListAction;
use App\Actions\PartsList\PartsListDefaultAction;
use App\Actions\PartsList\UpdatePartsListAction;
use App\Http\Controllers\Controller;
use App\Models\PartList;
use Illuminate\Http\Request;

class PartsListController extends Controller
{
    public function getDefaultPartsList(): \Illuminate\Http\JsonResponse
    {
        $list = app()->make(PartsListDefaultAction::class)->handle();
        return response()->json($list);
    }

    public function updatePart(Request $request, PartList $partList): \Illuminate\Http\JsonResponse
    {
        app()->make(UpdatePartsListAction::class)->handle($request->toArray(), $partList);
        return response()->json([], 202);
    }

    public function createPart(Request $request): \Illuminate\Http\JsonResponse
    {
        app()->make(CreateNewPartListAction::class)->handle($request->except(['id', 'is_new']));
        return response()->json([], 202);
    }

    public function deletePart(PartList $partList): \Illuminate\Http\JsonResponse
    {
        app()->make(DeletePartsListAction::class)->handle($partList);
        return response()->json([], 202);
    }

    public function addPart(Request $request, PartList $partList): \Illuminate\Http\JsonResponse
    {
        app()->make(AddNewPartsListAction::class)->handle($request->except(['parent_id']), $partList);
        return response()->json([], 202);
    }
}
