<?php

namespace App\Http\Resources\BaseCar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseCarFullResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $year_from_str = str_pad($this->month_start,2,0,STR_PAD_LEFT) . '.'.
            $this->year_start;
        if ($this->month_stop && $this->year_stop) {
            $year_end_str = str_pad($this->month_stop,2,0,STR_PAD_LEFT) . '.'.
                $this->year_stop;
        } else {
            $year_end_str = 'now';
        }

        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'generation_number' => $this->generation_number,
            'body_type' => $this->body_type,
            'doors' => $this->doors,
            'month_start' => $this->month_start,
            'month_stop' => $this->month_stop,
            'year_start' => $this->year_start,
            'year_stop' => $this->year_stop,
            'restyle' => $this->restyle,
            'not_restyle' => $this->not_restyle,
            'header' => $this->header,
            'years_string' => $year_from_str . '-' . $year_end_str,
            'pdr' => $this->nomenclatureBaseItem->baseItemPDR ?
                    $this->buildTree($this->nomenclatureBaseItem->baseItemPDR) :
                    [],
        ];
    }

    private function buildTree($itemPdr): array
    {
        return $this->recursivePDRTree($itemPdr->toArray());
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
