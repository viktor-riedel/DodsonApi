<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'model' => $this['model'],
            'preview_image' => $this['preview_image'],
            'generations' => $this['generations'],
        ];
    }
}
