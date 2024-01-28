<?php

namespace App\Http\Resources\AvailableCars;

use App\Http\Resources\BaseItem\BaseItemPdrPositionModificationResource;
use App\Http\Resources\BaseItem\BaseItemPositionPhotosResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'generation' => $this->generation,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
            'oem_number' => $this->oem_number,
            'ic_description' => $this->ic_description,
            'photos' => BaseItemPositionPhotosResource::collection($this->photos),
            'modifications' => BaseItemPdrPositionModificationResource::collection($this->modifications),
        ];
    }
}
