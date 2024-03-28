<?php

namespace App\Actions\FindPart;

use Illuminate\Support\Collection;

class FindPartMakesAction
{
    public function handle(): Collection
    {
        return \DB::table('nomenclature_base_item_pdr_cards')
            ->selectRaw('
                distinct nomenclature_base_items.make
            ')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.id',
                '=',
                'nomenclature_base_item_pdr_cards.id')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.id', '=',
                'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->join('nomenclature_base_items', 'nomenclature_base_items.id', '=',
                'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->whereNull('nomenclature_base_item_pdr_cards.deleted_at')
            ->orderBy('nomenclature_base_items.make')
            ->get();
    }
}
