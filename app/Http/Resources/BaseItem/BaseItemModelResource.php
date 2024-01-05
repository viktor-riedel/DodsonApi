<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'preview_image' => $this->preview_image,
        ];
    }
}
