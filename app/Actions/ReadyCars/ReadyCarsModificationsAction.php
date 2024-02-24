<?php

namespace App\Actions\ReadyCars;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReadyCarsModificationsAction
{
    public function handle(string $make, string $model, string $generation): Collection
    {
        $modifications = DB::table('nomenclature_base_item_modifications')
            ->selectRaw('image_url, body_type, chassis, transmission,
                    year_from, year_to, month_from, month_to,
                    restyle, drive_train, header, engine_type, engine_size, doors, engine_name,
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
                    'drive_train', 'header', 'restyle',
                    'engine_type', 'engine_size', 'doors', 'engine_name',
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
                $item->parts_count = $this->getPartsCount($make, $model, $generation, $item);
            });
        return $modifications;
    }

    private function getPartsCount(string $make, string $model, string $generation, $modification): int
    {
        return DB::table('nomenclature_base_item_pdrs')
            ->selectRaw('distinct nomenclature_base_item_pdr_positions.id')
            ->join('nomenclature_base_item_pdr_positions',
                'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id',
                '=',
                'nomenclature_base_item_pdrs.id')
            ->join('nomenclature_base_items', 'nomenclature_base_items.id', '=' , 'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->join('nomenclature_base_item_modifications',
                    'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id',
                    '=',
                    'nomenclature_base_item_pdr_positions.id')
            ->where('nomenclature_base_items.make', $make)
            ->where('nomenclature_base_items.model', $model)
            ->where('nomenclature_base_items.generation', $generation)
            ->whereNull('nomenclature_base_items.deleted_at')
            ->whereNull('nomenclature_base_item_pdrs.deleted_at')
            ->when($modification, function ($query) use ($modification) {
                return $query->where('nomenclature_base_item_modifications.header', $modification->header)
                ->when(isset($modification->restyle), function($q) use ($modification) {
                    return $q->where('nomenclature_base_item_modifications.restyle', $modification->restyle);
                })->when(!isset($modification->restyle), function ($q) {
                    return $q->whereNull('nomenclature_base_item_modifications.restyle');
                });
            })
            ->where('nomenclature_base_item_pdr_positions.is_virtual', false)
            ->whereNull('nomenclature_base_item_pdr_positions.deleted_at')
            ->get()
            ->count();
    }
}
