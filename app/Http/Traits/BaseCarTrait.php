<?php

namespace App\Http\Traits;

use App\Http\Resources\ContrAgents\ContrAgentResource;
use App\Models\ContrAgent;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureBaseItemPdrPositionPhoto;
use App\Models\PartList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait BaseCarTrait
{
    public function makes(): \Illuminate\Http\JsonResponse
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
                'image_url' => $positions->where('make', $make)->first()->preview_image
            ];
        }
        return response()->json($result);
    }

    public function models(string $make): \Illuminate\Http\JsonResponse
    {
        $result = [];
        $positions = NomenclatureBaseItem::with('NomenclaturePositionsNotVirtual')
            ->where('make', $make)
            ->get()
            ->filter(function($item) {
                return count($item->NomenclaturePositionsNotVirtual);
            });
        $models = array_unique($positions->pluck('model')->toArray());
        foreach ($models as $model) {
            $result[] = [
                'model' => $model,
                'generations' => $positions->where('make', $make)->where('model', $model)->count(),
                'image_url' => $positions->where('make', $make)->first()->preview_image
            ];
        }
        return response()->json($result);
    }

    public function generations(string $make, string $model): \Illuminate\Http\JsonResponse
    {
        $generations = NomenclatureBaseItem::where('make', $make)
            ->where('model', $model)
            ->get()
            ->pluck('generation')
            ->toArray();
        $generations = array_unique($generations);
        sort($generations);
        $generations = collect($generations)->transform(function($generation) use ($make, $model) {
            return [
                'model' => $model,
                'make' => $make,
                'generation' => $generation,
                'image_url' => NomenclatureBaseItem::where('make', $make)
                    ->where('model', $model)
                    ->where('generation', $generation)
                    ->first()->preview_image
            ];
        });
        return response()->json($generations);
    }

    public function modifications(string $make, string $model, string $generation): \Illuminate\Http\JsonResponse
    {
        $baseItem = NomenclatureBaseItem::with(
                'baseItemPDR',
                         'nomenclaturePositions',
                         'nomenclaturePositions.nomenclatureBaseItemPdrCard',
                         'nomenclaturePositions.modifications')
            ->where('make', $make)
            ->where('model', $model)
            ->where('generation', $generation)
            ->first();

        foreach($baseItem->modifications as $mod) {
            $needs = \DB::table('nomenclature_base_item_pdr_positions')
                ->selectRaw('
                sum(needs) as needs, sum(nz_needs) as needs_nz,
	            sum(ru_needs) as needs_ru, sum(mng_needs) as needs_mng, 
	            sum(jp_needs) as needs_jp            
            ')
                ->join('nomenclature_modifications', 'nomenclature_modifications.modificationable_id',
                    '=', 'nomenclature_base_item_pdr_positions.id')
                ->join('nomenclature_base_item_pdr_cards', 'nomenclature_base_item_pdr_cards.nomenclature_base_item_pdr_position_id',
                    '=', 'nomenclature_base_item_pdr_positions.id')
                ->where('nomenclature_modifications.inner_id', $mod->inner_id)
                ->whereNull('nomenclature_base_item_pdr_cards.deleted_at')
                ->first();

            $mod->needs = $needs->needs;
            $mod->needs_ru = $needs->needs_ru;
            $mod->needs_nz = $needs->needs_nz;
            $mod->needs_mng = $needs->needs_mng;
            $mod->needs_jp = $needs->needs_jp;
        }

        return response()->json($baseItem->modifications);
    }

    public function partsList(Request $request,
        string $make,
        string $model,
        string $generation,
        string $modification
    ): \Illuminate\Http\JsonResponse
    {
        $cards = \DB::table('nomenclature_base_item_pdr_positions')
            ->selectRaw('distinct nomenclature_base_item_pdr_positions.id,
                                   nomenclature_base_item_pdrs.item_name_eng,
                                   nomenclature_base_item_pdrs.item_name_ru,
                                   nomenclature_base_item_pdr_positions.ic_number,
                                   nomenclature_base_item_pdr_positions.oem_number,
                                   nomenclature_base_item_pdr_positions.ic_description,
                                   null as comment,
                                   nomenclature_modifications.generation')
            ->join('nomenclature_modifications', 'nomenclature_modifications.modificationable_id',
                '=', 'nomenclature_base_item_pdr_positions.id')
            ->join('nomenclature_base_item_pdr_cards',
                'nomenclature_base_item_pdr_cards.nomenclature_base_item_pdr_position_id',
                '=', 'nomenclature_base_item_pdr_positions.id')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.id',
                '=', 'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->whereNull('nomenclature_modifications.deleted_at')
            ->where('nomenclature_modifications.inner_id', $modification)
            ->where('nomenclature_base_item_pdr_positions.is_virtual', false)
            ->whereNull('nomenclature_base_item_pdr_cards.deleted_at')
            ->get()->each(function ($item) {
                $item->photos = NomenclatureBaseItemPdrPositionPhoto::where('nomenclature_base_item_pdr_position_id',
                    $item->id)->get();
                $item->modifications = NomenclatureBaseItemModification::where('nomenclature_base_item_pdr_position_id',
                    $item->id)->get();
                $item->card = NomenclatureBaseItemPdrCard::where('nomenclature_base_item_pdr_position_id',
                    $item->id)->first();
            });

        return response()->json($cards);
    }

    public function contrAgents(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ContrAgentResource::collection(ContrAgent::orderBy('name')->get());
    }

    public function miscPartsList(): \Illuminate\Http\JsonResponse
    {
        $list = PartList::where("is_folder", 0)
            ->where("is_virtual", 0)
            ->orderBy('item_name_eng')
            ->get(["id", "parent_id", "item_name_eng", "item_name_ru"]);

        return response()->json($list);
    }

    public function slicedMiscPartsList(): \Illuminate\Http\JsonResponse
    {
        $parts = [
            'BONNET',
            'BOOTLID/TAILGATE',
            'ENGINE WITH GEARBOX',
            'FRONT BUMPER',
            'FRONT END ASSY',
            'GRILLE',
            'LEFT DOOR MIRROR',
            'LEFT FRONT DOOR',
            'LEFT GUARD',
            'LEFT GUARD LINER',
            'LEFT HEADLAMP',
            'LEFT REAR DOOR',
            'LEFT TAILLIGHT',
            'REAR BUMPER',
            'RIGHT DOOR MIRROR',
            'RIGHT FRONT DOOR',
            'RIGHT GUARD',
            'RIGHT GUARD LINER',
            'RIGHT HEADLAMP',
            'RIGHT REAR DOOR',
            'RIGHT TAILLIGHT',
            'WASHER BOTTLE',
        ];
        $list = PartList::where('is_folder', 0)
            ->where("is_virtual", 0)
            ->whereIn('item_name_eng', $parts)
            ->orderBy('item_name_eng')
            ->get(["id", "is_folder", "parent_id", "item_name_eng", "item_name_ru"]);
        return response()->json($list);
    }
}
