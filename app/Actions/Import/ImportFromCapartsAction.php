<?php

namespace App\Actions\Import;

use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ImportFromCapartsAction
{
    public function handle(Request $request): int
    {
        ray($request->all());
        if ($request->input('mvr') && is_array($request->input('mvr'))) {
            $make = $request->input('mvr.make');
            $model = $request->input('mvr.model');
            $year = $request->input('mvr.year');
            $volume = $request->input('mvr.engine_size');
            $generation = $request->input('mvr.generation_number');
            $transmission_name = $request->input('mvr.transmission_name');
            $drive = $request->input('mvr.drive');
            $doors = $request->input('mvr.doors');
            $engine_type = $request->input('mvr.engine_type');
            $year_start = $request->input('period_start');
            $year_stop = $request->input('period_end');
            $body_type = $request->input('configuration');

            $baseCar = NomenclatureBaseItem::where([
                'make' => $make,
                'model' => $model,
            ])->first();

            if ($baseCar) {
                $modifications = $this->findModifications($baseCar->id);
                ray($modifications);
            }
        }
        return -1;
    }

    public function findModifications(int $baseItemId): Collection
    {
        return \DB::table('nomenclature_base_items')
            ->selectRaw('nomenclature_base_item_modifications.*')
            ->join('nomenclature_base_item_pdrs',
                'nomenclature_base_item_pdrs.nomenclature_base_item_id', '=', 'nomenclature_base_items.id')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdrs.id', '=',
            'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->join('nomenclature_base_item_modifications', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id', '=',
            'nomenclature_base_item_pdr_positions.id')
            ->where('nomenclature_base_items.id', $baseItemId)
            ->get();
    }
}
