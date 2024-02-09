<?php

namespace App\Http\Resources\AvailableCars;

use App\Http\Resources\BaseItem\BaseItemPdrPositionModificationApiResource;
use App\Http\Resources\BaseItem\BaseItemPositionPhotosApiResource;
use App\Http\Resources\BaseItemPdrPositionCardApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'base_item_id' => $this->card->nomenclature_base_item->inner_id,
            'generation' => $this->generation,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => $this->ic_description,
            'photos' => BaseItemPositionPhotosApiResource::collection($this->photos),
            'modifications' => BaseItemPdrPositionModificationApiResource::collection($this->modifications),
            'card' => new BaseItemPdrPositionCardApiResource($this->card),
        ];
    }
}
