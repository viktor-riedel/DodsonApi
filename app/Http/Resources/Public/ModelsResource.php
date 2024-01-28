<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'model' => $this['model'],
            'generations' => GenerationsResource::collection($this['generations']),
        ];
    }
}
