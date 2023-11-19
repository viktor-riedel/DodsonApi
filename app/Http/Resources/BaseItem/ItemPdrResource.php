<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemPdrResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomenclature_base_item_id' => $this->nomenclature_base_item_id,
            'parent_id' => $this->parent_id,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'is_folder' => $this->is_folder,
            'is_deleted' => $this->is_deleted,
        ];
    }
}
