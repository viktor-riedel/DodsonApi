<?php

namespace App\Actions\FindPart;

use Illuminate\Support\Collection;

class FindPartGenerationsAction
{
    public function handle(string $make, string $model): Collection
    {
        return \DB::table('nomenclature_base_item_pdr_cards')
            ->selectRaw('
                distinct nomenclature_base_items.generation
            ')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.id',
                '=',
                'nomenclature_base_item_pdr_cards.id')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.id', '=',
                'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->join('nomenclature_base_items', 'nomenclature_base_items.id', '=',
                'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->whereNull('nomenclature_base_item_pdr_cards.deleted_at')
            ->where('nomenclature_base_items.make', $make)
            ->where('nomenclature_base_items.model', $model)
            ->orderBy('nomenclature_base_items.make')
            ->get();
    }
}
