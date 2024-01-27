<?php

namespace App\Http\Controllers\Nomenclature;

use App\Actions\BaseItem\BaseItemModificationsSyncAction;
use App\Actions\BaseItemPosition\BaseItemIcListAction;
use App\Actions\BaseItemPosition\CreateBaseItemPositionAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseItem\BaseItemPdrPositionResource;
use App\Http\Resources\ItemPdrPositionListResource;
use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrPosition;
use Illuminate\Http\Request;

class BaseItemPdrPositionController extends Controller
{

    use BaseItemPdrTreeTrait;

    public function list(NomenclatureBaseItemPdr $baseItemPdr): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $positions = $baseItemPdr->nomenclatureBaseItemPdrPositions
            ->load('nomenclatureBaseItemPdrCard')
            ->load('photos')
            ->load('markets');
        return BaseItemPdrPositionResource::collection($positions);
    }

    public function icList(NomenclatureBaseItem $baseItemPdr): \Illuminate\Http\JsonResponse
    {
        $icList = $this->buildPdrTreeWithoutEmpty($baseItemPdr->baseItemPDR);
        return response()->json($icList);
    }

    public function listView(NomenclatureBaseItem $baseItemPdr): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $ic_list = app()->make(BaseItemIcListAction::class)->handle($baseItemPdr);
        return ItemPdrPositionListResource::collection($ic_list);
    }

    public function loadItemPosition(NomenclatureBaseItemPdrPosition $itemPosition): BaseItemPdrPositionResource
    {
        $itemPosition->with('nomenclatureBaseItemPdrCard', 'photos', 'markets');
        return new BaseItemPdrPositionResource($itemPosition);
    }

    public function create(Request $request, NomenclatureBaseItemPdr $baseItemPdr): BaseItemPdrPositionResource
    {
        $position = app()->make(CreateBaseItemPositionAction::class)->handle($request, $baseItemPdr);
        if ($position) {
            app()->make(BaseItemModificationsSyncAction::class)->handle($baseItemPdr, $position);
            return new BaseItemPdrPositionResource($position->load('nomenclatureBaseItemPdrCard'));
        }
        abort('Item has not been created');
    }

    public function delete(NomenclatureBaseItemPdrPosition $baseItemPdrPosition): \Illuminate\Http\JsonResponse
    {
        $item = NomenclatureBaseItemPdrPosition::whereHas('relatedPositions', function($q) use ($baseItemPdrPosition) {
            $q->where('related_id', $baseItemPdrPosition->id);
        })->first();
        $item?->relatedPositions()->detach($baseItemPdrPosition);
        $baseItemPdrPosition->delete();
        return response()->json([], 202);
    }

    public function update(Request $request, NomenclatureBaseItemPdrPosition $baseItemPdrPosition): \Illuminate\Http\JsonResponse
    {
        if (strtoupper($baseItemPdrPosition->ic_number) !== strtoupper($request->input('ic_number'))) {
            $exist = NomenclatureBaseItemPdrPosition::whereHas('nomenclatureBaseItemPdr', function( $q) use ($request) {
                $q->where('item_name_eng', strtoupper($request->input('name_eng')));
            })->where('ic_number', $request->input('ic_number'))->first();
            if ($exist) {
                abort(403, 'IC Number '. $request->input('ic_number') .' already exists');
            }
        }
        $baseItemPdrPosition->nomenclatureBaseItemPdrCard()->update($request->except('id', 'nomenclature_base_item_pdr_position_id'));
        $baseItemPdrPosition->nomenclatureBaseItemPdr()->update([
            'item_name_eng' => strtoupper($request->input('name_eng')),
            'item_name_ru' => mb_strtoupper($request->input('name_ru')),
        ]);
        $baseItemPdrPosition->update([
            'ic_number' => strtoupper($request->input('ic_number')),
            'oem_number' => strtoupper($request->input('oem_number') ?? 'n/a'),
            'ic_description' => $request->input('description'),
        ]);
        return response()->json([], 202);
    }

    public function updatePosition(Request $request, NomenclatureBaseItemPdrPosition $baseItemPdrPosition): \Illuminate\Http\JsonResponse
    {
        if ($baseItemPdrPosition->ic_number !== $request->input('ic_number')) {
            $exist = NomenclatureBaseItemPdrPosition::where('ic_number', $request->input('ic_number'))->first();
            if ($exist) {
                abort(403, 'IC Number already exists');
            }
        }
        $baseItemPdrPosition->update($request->except('id', 'card'));
        return response()->json([], 202);
    }
}
