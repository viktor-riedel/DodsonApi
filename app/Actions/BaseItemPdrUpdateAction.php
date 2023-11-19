<?php

namespace App\Actions;

use App\Models\NomenclatureBaseItemPdr;

class BaseItemPdrUpdateAction
{
    public function handle(array $pdr): void
    {
        foreach ($pdr as $element) {
            $itemPdr = NomenclatureBaseItemPdr::find($element['id']);
            $itemPdr->update([
                'item_name_eng' => $element['item_name_eng'],
                'item_name_ru' => $element['item_name_ru'],
                'is_folder' => $element['is_folder'],
                'is_deleted' => $element['is_deleted'],
            ]);
            if (isset($element['children'])) {
                $this->handleRecursiveUpdate($element['children']);
            }
        }
    }

    private function handleRecursiveUpdate(array $elements): void
    {
        foreach ($elements as $item) {
            $itemPdr = NomenclatureBaseItemPdr::find($item['id']);
            $itemPdr->update([
                'item_name_eng' => $item['item_name_eng'],
                'item_name_ru' => $item['item_name_ru'],
                'is_folder' => $item['is_folder'],
                'is_deleted' => $item['is_deleted'],
            ]);
            if (isset($item['children'])) {
                $this->handleRecursiveUpdate($item['children']);
            }
        }
    }
}
