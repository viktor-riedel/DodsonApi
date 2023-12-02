<?php

namespace App\Http\Controllers\Nomenclature;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseItem\BaseItemPdrPositionResource;
use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureCard;
use Illuminate\Http\Request;

class BaseItemPdrPositionController extends Controller
{
    public function list(NomenclatureBaseItemPdr $baseItemPdr): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $positions = $baseItemPdr->nomenclatureBaseItemPdrPositions
            ->load('nomenclatureBaseItemPdrCard')
            ->load('photos')
            ->load('markets');
        return BaseItemPdrPositionResource::collection($positions);
    }

    public function loadItemPosition(NomenclatureBaseItemPdrPosition $itemPosition): BaseItemPdrPositionResource
    {
        $itemPosition->with('nomenclatureBaseItemPdrCard', 'photos', 'markets');
        return new BaseItemPdrPositionResource($itemPosition);
    }

    public function create(Request $request, NomenclatureBaseItemPdr $baseItemPdr): BaseItemPdrPositionResource
    {
        $itemPosition= $baseItemPdr->nomenclatureBaseItemPdrPositions()->create($request->toArray());
        $itemPosition->load('nomenclatureBaseItemPdr');
        $default_card = NomenclatureCard::firstOrCreate();
        $itemPosition->nomenclatureBaseItemPdrCard()->create([
            'name_eng' => $itemPosition->nomenclatureBaseItemPdr->item_name_eng,
            'name_ru' => $itemPosition->nomenclatureBaseItemPdr->item_name_ru,
            'default_price' => $default_card->default_price,
            'default_retail_price' => $default_card->default_retail_price,
            'default_wholesale_price' => $default_card->default_wholesale_price,
            'default_special_price' => $default_card->default_special_price,
            'wholesale_rus_price' => $default_card->wholesale_rus_price,
            'wholesale_nz_price' => $default_card->wholesale_nz_price,
            'retail_rus_price' => $default_card->retail_rus_price,
            'retail_nz_price' => $default_card->retail_nz_price,
            'special_rus_price' => $default_card->special_rus_price,
            'special_nz_price' => $default_card->special_nz_price,
            'comment' => $default_card->comment,
            'description' => $itemPosition->ic_description,
            'status' => $default_card->status,
            'condition' => $default_card->condition,
            'tag' => $default_card->tag,
            'yard' => $default_card->yard,
            'bin' => $default_card->bin,
            'is_new' => $default_card->is_new,
            'is_scrap' => $default_card->is_scrap,
            'ic_number' => $itemPosition->ic_number,
            'oem_number' => $itemPosition->oem_number,
            'inner_number' => $default_card->inner_number,
            'color' => $default_card->color,
            'weight' => $default_card->weight,
            'extra' => $default_card->extra,
            'created_by' => null,
            'deleted_by' => null,
        ]);
        return new BaseItemPdrPositionResource($itemPosition->load('nomenclatureBaseItemPdrCard'));
    }

    public function delete(NomenclatureBaseItemPdrPosition $baseItemPdrPosition)
    {
        $baseItemPdrPosition->delete();
        return response()->json([], 202);
    }

    public function update(Request $request, NomenclatureBaseItemPdrPosition $baseItemPdrPosition)
    {
        $baseItemPdrPosition->nomenclatureBaseItemPdrCard()->update($request->except('id', 'nomenclature_base_item_pdr_position_id'));
        $baseItemPdrPosition->nomenclatureBaseItemPdr()->update([
            'item_name_eng' => $request->input('name_eng'),
            'item_name_ru' => $request->input('name_ru'),
        ]);
        $baseItemPdrPosition->update([
            'ic_number' => $request->input('ic_number'),
            'oem_number' => $request->input('oem_number'),
            'ic_description' => $request->input('description'),
        ]);
        return response()->json([], 202);
    }

    public function updatePosition(Request $request, NomenclatureBaseItemPdrPosition $baseItemPdrPosition)
    {
        $baseItemPdrPosition->update($request->except('id', 'card'));
        return response()->json([], 202);
    }
}
