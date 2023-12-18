<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'generation_number' => $this->generation_number,
            'preview_image' => $this->preview_image,
            'item_pdr' => $this->buildPdrTree($this->baseItemPDR),
            'restyle' => $this->restyle,
            'not_restyle' => $this->not_restyle,
        ];
    }

    private function buildPdrTree($itemPDR): array
    {
        $itemPDR->load('nomenclatureBaseItemPdrPositions');
        return $this->recursivePDRTree($itemPDR->toArray());
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
                $el['key'] = $el['parent_id'] . '-'. $el['id'];
                $el['positions_count'] = count($el['nomenclature_base_item_pdr_positions']);
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
