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
        $pdr->load('nomenclatureBaseItemPdrPositions');
        return $this->getPDRTreeExcludingEmpty($pdr->toArray());
    }

    private function getPDRTreeExcludingEmpty(array $elements, $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $el) {
            if ($el['parent_id'] === $parent_id) {
                $children = $this->getPDRTreeExcludingEmpty($elements, $el['id']);
                if (count($children)) {
                    $el['children'] = $children;
                }

                if ($el['is_folder']) {
                    $el['icon'] = 'pi pi-pw pi-folder';
                } else {
                    $el['icon'] = 'pi pi-fw pi-cog';
                }
                $el['photos'] = $this->getPhotos([$el]);

                $count = 0;
                foreach($el['nomenclature_base_item_pdr_positions'] as $element) {
                    if (!$element['is_virtual']) {
                        $count++;
                    }

                }
                $el['key'] = $el['parent_id'] . '-'. $el['id'];
                $el['positions_count'] = $count;
                if (isset($el['children'])) {
                    $el['children'] = $this->checkEmptyChildren($el['children']);
                }
                if (isset($el['children']) && $el['is_folder'] && $el['positions_count'] === 0 && count($el['children']) === 0) {
                    continue;
                }

                if (isset($el['children']) && !$el['is_folder'] && $el['positions_count'] === 0 && count($el['children']) === 0) {
                    continue;
                }

                $branch[] = $el;
            }
        }
        return $branch;
    }

    private function checkEmptyChildren(array $children): array
    {
        $kids = [];
        foreach($children as $child) {
            if (!isset($child['children']) && count($child['nomenclature_base_item_pdr_positions'])) {
                $kids[] = $child;
            }
            if (isset($child['children']) && count($child['children'])) {
                $kids = $this->checkEmptyChildren($child['children']);
            }
        }
        return $kids;
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
