<?php

namespace App\Actions\FindPart;

use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureBaseItemPdrPosition;
use App\Models\NomenclatureBaseItemPdrPositionPhoto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FindPartAction
{
    public function handle(string $page): LengthAwarePaginator
    {
        $data = \DB::table('nomenclature_base_item_pdr_cards')
            ->selectRaw('
                nomenclature_base_item_pdr_cards.id,
                nomenclature_base_item_pdr_cards.inner_id,
                nomenclature_base_item_pdr_cards.name_eng,
                nomenclature_base_item_pdr_cards.name_ru,
                nomenclature_base_item_pdr_cards.description,
                nomenclature_base_item_pdr_cards.ic_number,
                nomenclature_base_item_pdr_cards.oem_number,
                nomenclature_base_items.make,
                nomenclature_base_items.model,
                nomenclature_base_items.generation, 
                nomenclature_base_items.preview_image,
                nomenclature_base_item_pdr_positions.id as position_id
            ')
            ->leftJoin('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.id',
            '=',
            'nomenclature_base_item_pdr_cards.id')
            ->leftJoin('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.id', '=',
            'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->leftJoin('nomenclature_base_items', 'nomenclature_base_items.id', '=',
            'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->whereNull('nomenclature_base_item_pdr_cards.deleted_at')
            ->orderBy('nomenclature_base_item_pdr_cards.name_eng')
            ->paginate(20);

        return $data;
    }
}
