<?php

namespace App\Http\Traits;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureBaseItemPdrPositionPhoto;
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
        return response()->json($makes);
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
        $modifications = DB::table('nomenclature_base_item_modifications')
            ->selectRaw('image_url, body_type, chassis, transmission,
                    year_from, year_to, month_from, month_to,
                    restyle, drive_train, header, doors, engine_size,
                    nomenclature_base_item_modifications.generation, 
                    sum(nomenclature_base_item_pdr_cards.needs) as needs,
                    sum(nomenclature_base_item_pdr_cards.ru_needs) as needs_ru,
                    sum(nomenclature_base_item_pdr_cards.nz_needs) as needs_nz,
                    sum(nomenclature_base_item_pdr_cards.mng_needs) as needs_mng,
                    sum(nomenclature_base_item_pdr_cards.jp_needs) as needs_jp')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.id', '=', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id')
            ->join('nomenclature_base_item_pdr_cards', 'nomenclature_base_item_pdr_cards.nomenclature_base_item_pdr_position_id', '=', 'nomenclature_base_item_pdr_positions.id')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.id', '=', 'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->join('nomenclature_base_items', 'nomenclature_base_items.id', '=', 'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->where('nomenclature_base_items.make', $make)
            ->where('nomenclature_base_items.model', $model)
            ->where('nomenclature_base_items.generation', $generation)
            ->whereNull('nomenclature_base_items.deleted_at')
            ->whereNull('nomenclature_base_item_pdrs.deleted_at')
            ->groupBy('image_url', 'body_type', 'chassis', 'transmission',
                'year_from', 'year_to', 'month_from', 'month_to', 'restyle',
                'drive_train', 'header', 'restyle', 'doors', 'engine_size',
                'nomenclature_base_item_modifications.generation')
            ->orderBy('year_from')
            ->orderBy('year_to')
            ->get()->each(function($item) {
                $year_from_str = str_pad($item->month_from,2,0,STR_PAD_LEFT) . '.'.
                    $item->year_from;
                if ($item->month_to && $item->year_to) {
                    $year_end_str = str_pad($item->month_to,2,0,STR_PAD_LEFT) . '.'.
                        $item->year_to;
                } else {
                    $year_end_str = 'now';
                }
                $item->years_string = $year_from_str . '-' . $year_end_str;
            });

        return response()->json($modifications);
    }

    public function partsList(Request $request, string $make, string $model, string $generation): \Illuminate\Http\JsonResponse
    {
        $query = NomenclatureBaseItem::query();
        $query->where(['make' => $make, 'model' => $model, 'generation' => $generation]);
        $modification = $request->toArray();
        $baseItemsIds = $query->get()->pluck('id')->toArray();

        $data = DB::table('nomenclature_base_item_pdrs')
            ->selectRaw('distinct nomenclature_base_item_pdr_positions.id,
                                   nomenclature_base_item_pdrs.item_name_eng,
                                   nomenclature_base_item_pdrs.item_name_ru,
                                   nomenclature_base_item_pdr_positions.ic_number,
                                   nomenclature_base_item_pdr_positions.oem_number,
                                   nomenclature_base_item_pdr_positions.ic_description,
                                   nomenclature_base_items.generation')
            ->join('nomenclature_base_item_pdr_positions',
                'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id',
                '=',
                'nomenclature_base_item_pdrs.id')
            ->join('nomenclature_base_items', 'nomenclature_base_items.id', '=' , 'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->join('nomenclature_base_item_modifications',
                    'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id',
                    '=',
                    'nomenclature_base_item_pdr_positions.id')
            ->whereIn('nomenclature_base_item_id', $baseItemsIds)
            ->where('nomenclature_base_item_modifications.header', $modification['header'])
            ->when($request->input('generation'), function($q)  use ($request) {
                return $q->where('nomenclature_base_item_modifications.generation', $request->input('generation'));
            })
            ->when(!isset($modification['restyle']), function($q) {
                return $q->whereNull('nomenclature_base_item_modifications.restyle');
            })
            ->when(isset($modification['restyle']), function ($q) use ($modification) {
                return $q->where('nomenclature_base_item_modifications.restyle', (int) $modification['restyle']);
            })
            ->where('nomenclature_base_item_pdr_positions.is_virtual', false)
            ->whereNull('nomenclature_base_item_pdrs.deleted_at')
            ->get()
            ->each(function($item) {
                $item->photos = NomenclatureBaseItemPdrPositionPhoto::where('nomenclature_base_item_pdr_position_id', $item->id)->get();
                $item->modifications = NomenclatureBaseItemModification::where('nomenclature_base_item_pdr_position_id', $item->id)->get();
                $item->card = NomenclatureBaseItemPdrCard::where('nomenclature_base_item_pdr_position_id', $item->id)->first();
            });

        return response()->json($data);
    }
}
