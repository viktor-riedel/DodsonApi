<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $year_month_start = $this->month_start . '.' . $this->year_start;
        $year_month_end = $this->month_stop . '.' . ($this->year_stop ?? 'now');
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'header' => $this->header,
            'generation' => $this->generation,
            'year_start' => $this->year_start,
            'year_stop' => $this->year_stop,
            'month_start' => $this->month_start,
            'month_stop' => $this->month_stop,
            'preview_image' => $this->preview_image,
            'restyle' => $this->restyle,
            'not_restyle' => $this->not_restyle,
            'doors' => $this->doors,
            'body_type' => $this->body_type,
            'engine_name' => $this->engine_name,
            'engine_type' => $this->engine_type,
            'engine_size' => $this->engine_size,
            'engine_power' => $this->engine_power,
            'transmission_type' => $this->transmission_type,
            'drive_train' => $this->drive_train,
            'chassis' => explode(',', $this->chassis),
            'year_month_start' => $year_month_start,
            'year_month_end' => $year_month_end,
            'item_pdr' => $this->buildPdrTree($this->baseItemPDR),
            'start_stop_dates' => $year_month_start . ' - ' . $year_month_end,
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
                if ($children) {
                    $el['icon'] = 'pi pi-pw pi-folder';
                    $el['children'] = $children;
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
}
