<?php

namespace App\Actions\BaseItem;

use App\Models\NomenclatureBaseItemPdr;
use App\Models\NomenclatureBaseItemPdrPosition;
use DB;

class BaseItemModificationsSyncAction
{
    public function handle(NomenclatureBaseItemPdr $baseItemPdr, NomenclatureBaseItemPdrPosition $position): void
    {
        $modifications = DB::table('nomenclature_base_items')
            ->selectRaw('distinct nomenclature_base_item_modifications.header,
                                    nomenclature_base_item_modifications.generation, 
                                    nomenclature_base_item_modifications.modification, 
                                    nomenclature_base_item_modifications.engine_name, 
                                    nomenclature_base_item_modifications.engine_type,
                                    nomenclature_base_item_modifications.engine_size, 
                                    nomenclature_base_item_modifications.engine_power, 
                                    nomenclature_base_item_modifications.doors, 
                                    nomenclature_base_item_modifications.transmission, 
                                    nomenclature_base_item_modifications.drive_train, 
                                    nomenclature_base_item_modifications.chassis,
                                    nomenclature_base_item_modifications.body_type, 
                                    nomenclature_base_item_modifications.image_url, 
                                    nomenclature_base_item_modifications.restyle, 
                                    nomenclature_base_item_modifications.not_restyle, 
                                    nomenclature_base_item_modifications.month_from, 
                                    nomenclature_base_item_modifications.month_to,
                                    nomenclature_base_item_modifications.year_from, 
                                    nomenclature_base_item_modifications.year_to')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.nomenclature_base_item_id', '=', 'nomenclature_base_items.id')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id', '=', 'nomenclature_base_item_pdrs.id')
            ->join('nomenclature_base_item_modifications', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id', '=', 'nomenclature_base_item_pdr_positions.id')
            ->where('nomenclature_base_item_pdrs.id', $baseItemPdr->id)
            ->whereNull('nomenclature_base_item_modifications.deleted_at')
            ->get();

        if ($modifications->count()) {
            foreach($modifications as $modification) {
                $position->nomenclatureBaseItemModifications()->create((array) $modification);
            }
        }
    }
}
