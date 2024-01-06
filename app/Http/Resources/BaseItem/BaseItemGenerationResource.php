<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemGenerationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'generation' => $this->generation,
            'preview_image' => $this->preview_image,
        ];
    }
}
