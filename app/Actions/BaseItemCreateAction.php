<?php

namespace App\Actions;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureCard;
use Illuminate\Http\Request;

class BaseItemCreateAction
{
    public function handle(Request $request): int
    {
        $item = NomenclatureBaseItem::where([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'header' => $request->input('header'),
            'generation' => $request->input('generation'),
            'year_start' => $request->input('year_start'),
            'year_stop' => $request->input('year_stop'),
            'month_start' => $request->input('month_start'),
            'month_stop' => $request->input('month_stop'),
        ])->first();
        abort_if($item !== null, 400, 'Item already exist');

        //create base item
        $nomenclatureBaseItem = NomenclatureBaseItem::create([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'header' => $request->input('header'),
            'generation' => $request->input('generation'),
            'year_start' => $request->input('year_start'),
            'year_stop' => $request->input('year_stop'),
            'month_start' => $request->input('month_start'),
            'month_stop' => $request->input('month_stop'),
            'preview_image' => $request->input('preview_image'),
            'restyle' => (bool) $request->input('restyle'),
            'not_restyle' => (bool) $request->input('not_restyle'),
            'doors' => $request->input('doors'),
            'body_type' => $request->input('body_type'),
            'engine_name' => $request->input('engine_name'),
            'engine_type' => $request->input('engine_type'),
            'engine_size' => $request->input('engine_size'),
            'engine_power' => $request->input('engine_power'),
            'transmission_type' => $request->input('transmission_type'),
            'drive_train' => $request->input('drive_train'),
            'chassis' => $request->input('chassis'),
            'created_by' => null,
            'deleted_by' => null,
        ]);

        //create pdr and cards
        $this->handleRecursiveCreation($request->input('pdr'), $nomenclatureBaseItem);
        return $nomenclatureBaseItem->id;
    }

    private function handleRecursiveCreation(array $elements, NomenclatureBaseItem $baseItem, int $parentId = 0): void
    {
        // for folders we create a virtual base item and card for it
        foreach ($elements as $element) {
            $baseItemPdr = $baseItem->baseItemPDR()->create([
                'parent_id' => $parentId,
                'item_name_eng' => $element['item_name_eng'],
                'item_name_ru' => $element['item_name_ru'],
                'is_folder' => $element['is_folder'],
                'is_deleted' => $element['is_deleted'] ?? false,
                'created_by' => null,
                'deleted_by' => null,
            ]);

            if (isset($element['is_folder']) && $element['is_folder']) {
                $basePosition = $baseItemPdr->nomenclatureBaseItemVirtualPosition()->create(
                    [
                        'nomenclature_base_item_pdr_id' => $baseItemPdr->id,
                        'ic_number' => 'virtual',
                        'oem_number' => 'virtual',
                        'ic_description' => 'virtual',
                        'is_virtual' => true,
                    ]
                );

                $baseItemPdr->update(['nomenclature_base_item_pdr_position_id' => $basePosition->id]);

                $basePosition->nomenclatureBaseItemPdrCard()->create([]);
            }

            if (isset($element['children'])) {
                $this->handleRecursiveCreation($element['children'], $baseItem, $baseItemPdr->id);
            }
        }
    }
}
