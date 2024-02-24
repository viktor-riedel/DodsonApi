<?php

namespace App\Http\Traits;

use App\Models\NomenclatureBaseItem;
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
                    nomenclature_base_item_modifications.generation')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.id', '=', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id')
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
            ->get()->each(function($item) use ($make, $model, $generation) {
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
}
