<?php

namespace App\Http\Resources\FindPart;

use App\Models\NomenclatureBaseItemModification;
use App\Models\NomenclatureBaseItemPdrCard;
use App\Models\NomenclatureBaseItemPdrPositionPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FindPartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position_id' => $this->position_id,
            'inner_id' => $this->inner_id,
            'name_eng' => $this->name_eng,
            'name_ru' => $this->name_ru,
            'description' => $this->description,
            'ic_number' => $this->ic_number,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'photos' => NomenclatureBaseItemPdrPositionPhoto::where('nomenclature_base_item_pdr_position_id', $this->position_id)->get(),
            'modifications' => NomenclatureBaseItemModification::where('nomenclature_base_item_pdr_position_id', $this->position_id)->get(),
            'card' => NomenclatureBaseItemPdrCard::find($this->id),
        ];
    }
}
