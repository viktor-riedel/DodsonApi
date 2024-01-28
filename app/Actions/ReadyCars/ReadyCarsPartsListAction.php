<?php

namespace App\Actions\ReadyCars;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureBaseItemPdrPositionPhoto;
use Illuminate\Support\Collection;
use DB;


class ReadyCarsPartsListAction
{
    public function handle(string $make, string $model, string $generation = '', string $modification = ''): Collection
    {
        $query = NomenclatureBaseItem::query();
        $query->where(['make' => $make, 'model' => $model]);
        $query->when(!empty($generation), function($q) use ($generation) {
            return $q->where('generation', $generation);
        });
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
            ->when($modification, function($query) {
                return $query->join('nomenclature_base_item_modifications',
                    'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id',
                    '=',
                    'nomenclature_base_item_pdr_positions.id');
            })
            ->whereIn('nomenclature_base_item_id', $baseItemsIds)
            ->when($modification, function($query) use ($modification) {
                return $query->where('nomenclature_base_item_modifications.header', $modification);
            })
            ->where('nomenclature_base_item_pdr_positions.is_virtual', false)
            ->whereNull('nomenclature_base_item_pdrs.deleted_at')
            ->get()
            ->each(function($item) {
                $item->photos = NomenclatureBaseItemPdrPositionPhoto::where('nomenclature_base_item_pdr_position_id', $item->id)->get();
                $item->modifications = NomenclatureBaseItemModification::where('nomenclature_base_item_pdr_position_id', $item->id)->get();
                $item->card = NomenclatureBaseItemPdrCard::where('nomenclature_base_item_pdr_position_id', $item->id)->first();
            });

        return $data;
    }
}
