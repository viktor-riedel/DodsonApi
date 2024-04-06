<?php

namespace App\Http\Traits;

use App\Models\NomenclatureBaseItem;

trait SyncBaseItemModificationsTrait
{
    protected function syncBaseItemModifications(NomenclatureBaseItem $nomenclatureBaseItem): void
    {
        $mods = \DB::table('nomenclature_base_items')
            ->selectRaw('distinct
                nomenclature_base_item_modifications.inner_id,
                nomenclature_base_item_modifications.header,
                nomenclature_base_item_modifications.generation,
                nomenclature_base_item_modifications.engine_name,
                nomenclature_base_item_modifications.engine_type,
                nomenclature_base_item_modifications.engine_size,
                nomenclature_base_item_modifications.engine_power,
                nomenclature_base_item_modifications.doors,
                nomenclature_base_item_modifications.transmission,
                nomenclature_base_item_modifications.drive_train,
                nomenclature_base_item_modifications.body_type,
                nomenclature_base_item_modifications.image_url,
                nomenclature_base_item_modifications.restyle,
                nomenclature_base_item_modifications.not_restyle,
                nomenclature_base_item_modifications.month_from,
                nomenclature_base_item_modifications.month_to,
                nomenclature_base_item_modifications.year_from,
                nomenclature_base_item_modifications.year_to'
            )
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.nomenclature_base_item_id',
            '=', 'nomenclature_base_items.id')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdrs.id',
            '=', 'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->join('nomenclature_base_item_modifications', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id',
            '=', 'nomenclature_base_item_pdr_positions.id')
            ->where([
                'nomenclature_base_items.make' => $nomenclatureBaseItem->make,
                'nomenclature_base_items.model' => $nomenclatureBaseItem->model,
                'nomenclature_base_items.generation' => $nomenclatureBaseItem->generation,
                'nomenclature_base_item_modifications.deleted_at' => null
            ])
            ->get();

        $nomenclatureBaseItem->modifications()->delete();
        if ($mods) {
            foreach ($mods as $modification) {
                $nomenclatureBaseItem->modifications()->create((array) $modification);
            }
        }
    }
}
