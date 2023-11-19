<?php

namespace App\Actions;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureCard;
use Illuminate\Http\Request;

class BaseItemCreateAction
{
    public function handle(Request $request): void
    {
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
        $defaultCard = NomenclatureCard::firstOrCreate([]);
        $this->handleRecursiveCreation($request->input('pdr'), $defaultCard, $nomenclatureBaseItem);
    }

    private function handleRecursiveCreation(array $elements, NomenclatureCard $card, NomenclatureBaseItem $baseItem, int $parentId = 0): void
    {
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

            $baseItemPdr->nomenclatureBaseItemPdrCard()->create([
                'name' => $card->name,
                'default_price' => $card->default_price,
                'default_retail_price' => $card->default_retail_price,
                'default_wholesale_price' => $card->default_wholesale_price,
                'default_special_price' => $card->default_special_price,
                'wholesale_rus_price' => $card->wholesale_rus_price,
                'wholesale_nz_price' => $card->wholesale_nz_price,
                'retail_rus_price' => $card->retail_rus_price,
                'retail_nz_price' => $card->retail_nz_price,
                'special_rus_price' => $card->special_rus_price,
                'special_nz_price' => $card->special_nz_price,
                'comment' => $card->comment,
                'description' => $card->description,
                'status' => $card->status,
                'condition' => $card->condition,
                'tag' => $card->tag,
                'yard' => $card->yard,
                'bin' => $card->bin,
                'is_new' => $card->is_new,
                'is_scrap' => $card->is_scrap,
                'ic_number' => $card->ic_number,
                'oem_number' => $card->oem_number,
                'inner_number' => $card->inner_number,
                'color' => $card->color,
                'weight' => $card->weight,
                'extra' => $card->extra,
                'created_by' => null,
                'deleted_by' => null,
            ]);

            if (isset($element['children'])) {
                $this->handleRecursiveCreation($element['children'], $card, $baseItem, $baseItemPdr->id);
            }
        }
    }
}
