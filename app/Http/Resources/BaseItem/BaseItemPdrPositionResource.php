<?php

namespace App\Http\Resources\BaseItem;

use App\Http\Resources\BaseItemPdrPositionCardResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemPdrPositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_eng' => $this->item_name_eng,
            'name_ru' => $this->item_name_ru,
            'nomenclature_base_item_pdr_id' => $this->nomenclature_base_item_pdr_id,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => $this->ic_description,
            'card' => $this->whenLoaded('nomenclatureBaseItemPdrCard',
                    new BaseItemPdrPositionCardResource($this->nomenclatureBaseItemPdrCard),
                    null),
            'photos' => $this->whenLoaded('photos',
                BaseItemPositionPhotosResource::collection($this->photos), []),
            'markets' => $this->whenLoaded('markets'),
            'is_virtual' => $this->is_virtual,
        ];
    }
}
