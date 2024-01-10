<?php

namespace App\Http\Resources\BaseItem;

use App\Http\Traits\BaseItemPdrTreeTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemResource extends JsonResource
{
    use BaseItemPdrTreeTrait;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'preview_image' => $this->preview_image,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'item_pdr' => $this->buildPdrTree($this->baseItemPDR),
        ];
    }
}
