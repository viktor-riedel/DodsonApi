<?php

namespace App\Actions\BaseItem;

use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdr;
use App\Models\PartList;

class BaseItemUpdatePartsList
{
    public function handle(array $request, NomenclatureBaseItem $baseItem): void
    {
        $partsList = $baseItem->baseItemPDR;
        if (!$partsList->count()) {
            //create new parts list
            $this->createNewPartsListRecursive($request, $baseItem);
        } else {
            //if $request parts list empty -> remove all from current
            if (!count($request)) {
                NomenclatureBaseItemPdr::where(
                    'nomenclature_base_item_id', $baseItem->id,
                )->delete();
            }
            //update current parts list
            $ids = $this->findPartsListRecursive($request);
            if (count($ids)) {
                $currentIds = NomenclatureBaseItemPdr::where(
                    'nomenclature_base_item_id', $baseItem->id,
                )->get()->pluck('parts_list_id')->toArray();

               $diffIds = array_diff($ids, $currentIds);

               if (count($diffIds)) {
                   foreach ($diffIds as $id) {
                       $item = NomenclatureBaseItemPdr::where(
                           'nomenclature_base_item_id', $baseItem->id,
                                )->where('parts_list_id', $id)->first();

                       if (!$item) {
                           //create new
                           $part = PartList::find($id);
                           if ($part->is_folder) {
                               foreach ($request as $item) {
                                   if ($item['id'] === $part->id) {
                                       if (!isset($item['parts_list_id'])) {
                                           $parentId = $this->createPositionWithCard($item, $baseItem);
                                           if (isset($item['children']) && count($item['children'])) {
                                               $this->createNewPartsListRecursive($item['children'], $baseItem, $parentId);
                                           }
                                       }
                                   }
                               }
                           } else {
                               $this->createPositionWithCard($part->toArray(), $baseItem);
                           }
                       }
                   }
               }
               //delete items
                foreach ($currentIds as $currentId) {
                    if (!in_array($currentId, $ids, true)) {
                        NomenclatureBaseItemPdr::where(
                            'nomenclature_base_item_id', $baseItem->id,
                        )->where('parts_list_id', $currentId)?->delete();
                    }
                }
            }
        }
    }

    private function findPartsListRecursive(array $parts, array &$partsIds = []): array
    {
        foreach ($parts as $part) {
            $partsIds[] = $part['parts_list_id'] ?? $part['id'];

            if (isset($part['children']) && count($part['children'])) {
                $this->findPartsListRecursive($part['children'], $partsIds);
            }
        }

        return $partsIds;
    }

    private function createNewPartsListRecursive(array $parts, NomenclatureBaseItem $baseItem, $parentId = 0): void
    {
        foreach ($parts as $part) {
            $id = $this->createPositionWithCard($part, $baseItem, $parentId);

            if (isset($part['children']) && count($part['children'])) {
                $this->createNewPartsListRecursive($part['children'], $baseItem, $id);
            }
        }
    }

    private function createPositionWithCard(array $part, NomenclatureBaseItem $baseItem, $parentId = 0): int
    {
        $baseItemPdr = $baseItem->baseItemPDR()->create([
            'parent_id' => $parentId,
            'item_name_eng' => $part['item_name_eng'],
            'item_name_ru' => $part['item_name_ru'],
            'is_folder' => $part['is_folder'],
            'is_deleted' => false,
            'parts_list_id' => $part['id'],
            'created_by' => null,
            'deleted_by' => null,
        ]);

        if ($part['is_folder']) {
            // for folders we create a virtual base item and card for it
            $position = $baseItemPdr->nomenclatureBaseItemVirtualPosition()->create(
                [
                    'nomenclature_base_item_pdr_id' => $baseItemPdr->id,
                    'item_name_eng' => $part['item_name_eng'],
                    'item_name_ru' => $part['item_name_ru'],
                    'ic_number' => 'virtual',
                    'oem_number' => 'virtual',
                    'ic_description' => 'virtual',
                    'is_virtual' => true,
                ]
            );
            $position->nomenclatureBaseItemPdrCard()->create([]);
            $baseItemPdr->update(['nomenclature_base_item_pdr_position_id' => $position->id]);
        }


        return $baseItemPdr->id;
    }
}
