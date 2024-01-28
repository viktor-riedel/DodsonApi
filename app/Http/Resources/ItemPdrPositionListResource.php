<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseItem\BaseItemPdrPositionModificationResource;
use App\Http\Resources\BaseItem\BaseItemPositionPhotosResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemPdrPositionListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name_eng' => $this->nomenclatureBaseItemPdr->item_name_eng,
            'item_name_ru' => $this->nomenclatureBaseItemPdr->item_name_ru,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => $this->ic_description,
            'photos' => $this->photos->count() ? BaseItemPositionPhotosResource::collection($this->photos) : [],
            'card' => new BaseItemPdrPositionCardResource($this->nomenclatureBaseItemPdrCard),
            'modifications' => BaseItemPdrPositionModificationResource::collection($this->nomenclatureBaseItemModifications),
        ];
    }
}
