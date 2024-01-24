<?php

namespace App\Http\Traits;

trait BaseItemPdrTreeTrait
{
    private function buildPdrTree($pdr): array
    {
        $pdr->load('nomenclatureBaseItemPdrPositions');
        return $this->recursivePDRTree($pdr->toArray());
    }

    private function recursivePDRTree(array $elements, $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $el) {
            if ($el['parent_id'] === $parent_id) {
                $children = $this->recursivePDRTree($elements, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }
                if ($el['is_folder']) {
                    $el['icon'] = 'pi pi-pw pi-folder';
                    $el['photos'] = $this->getPhotos([$el]);
                } else {
                    $el['icon'] = 'pi pi-fw pi-cog';
                }
                $count = 0;
                foreach($el['nomenclature_base_item_pdr_positions'] as $element) {
                    if (!$element['is_virtual']) {
                        $count++;
                    }
                }
                $el['key'] = $el['parent_id'] . '-'. $el['id'];
                $el['positions_count'] = $count;
                $branch[] = $el;
            }
        }

        return $branch;
    }

    private function buildPdrTreeWithoutEmpty($pdr): array
    {
        $tree = $this->buildPdrTree($pdr);
        return $this->deleteEmptyItemsFromTree($tree);
    }

    private function deleteEmptyItemsFromTree(array &$elements, $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $i => &$el) {
            if ($el['is_folder'] && isset($el['children']) && count($el['children'])) {
                $this->deleteEmptyItemsFromTree($el['children'], $el['id']);
            }
            if (!$el['is_folder'] && !count($el['nomenclature_base_item_pdr_positions'])) {
                unset($elements[$i]);
            } else if ($el['is_folder'] && isset($el['children']) && !count($el['children'])) {
                unset($elements[$i]);
            }
            else {
                $el['key'] = $parent_id . '-'. $el['id'];
                if (isset($el['children'])) {
                    $el['children'] = array_values($el['children']);
                }
                $branch[] = $el;
            }
        }
        return $branch;
    }

    private function getPhotos(array $elements, &$photos = []): array
    {
        foreach ($elements as $el) {
            if (isset($el['children']) && count($el['children'])) {
                $photos = $this->getPhotos($el['children'], $photos);
            }
            if (isset($el['nomenclature_base_item_virtual_position'])) {
                if (count($el['nomenclature_base_item_virtual_position']['photos'])) {
                    $photos = $el['nomenclature_base_item_virtual_position']['photos'];
                }
            }
        }

        return $photos;
    }
}
