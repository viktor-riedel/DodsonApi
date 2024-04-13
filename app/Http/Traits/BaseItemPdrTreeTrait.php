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

    private function buildPdrTreeWithoutEmpty($pdr, array $includeOnlyPositions = []): array
    {
        $tree = $this->buildPdrTree($pdr);
        // only use included
        $this->deleteEmptyItemsFromTree($tree);
        if (count($includeOnlyPositions)) {
            $this->includeOnlyPositions($tree, $includeOnlyPositions);
        }
        return $tree;
    }

    private function includeOnlyPositions(array &$elements, array $positions = []): void
    {
        foreach ($elements as &$el) {
            if ($el['is_folder'] && isset($el['children']) && count($el['children'])) {
                $this->includeOnlyPositions($el['children'], $positions);
            }
            if (isset($el['nomenclature_base_item_pdr_positions'])) {
                $pos = collect($el['nomenclature_base_item_pdr_positions'])->filter(function($item) use ($positions) {
                    return in_array($item['id'], $positions, true);
                });
                $el['nomenclature_base_item_pdr_positions'] = array_values($pos->toArray());
            }
            if (isset($el['children'])) {
                $el['children'] = array_values($el['children']);
            }
        }
    }

    private function deleteEmptyItemsFromTree(array &$elements, int $parent_id = 0): array
    {
        $branch = [];
        foreach ($elements as $i => &$el) {
            if ($el['is_folder'] && isset($el['children']) && count($el['children'])) {
                $this->deleteEmptyItemsFromTree($el['children'], $el['id']);
            }
            if (!$el['is_folder'] && !count($el['nomenclature_base_item_pdr_positions'])) {
                unset($elements[$i]);
            } else if ($el['is_folder'] && isset($el['children']) && !count($el['children'])) {
                if (!count($el['nomenclature_base_item_pdr_positions'])) {
                    unset($elements[$i]);
                }
            } else if ($el['is_folder'] && !isset($el['children']) && count($el['nomenclature_base_item_pdr_positions']) === 1) {
                if ($el['nomenclature_base_item_pdr_positions'][0]['is_virtual']) {
                    unset($elements[$i]);
                }
            } else {
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
