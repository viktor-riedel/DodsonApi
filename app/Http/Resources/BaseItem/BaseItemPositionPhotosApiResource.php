<?php

namespace App\Http\Resources\BaseItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseItemPositionPhotosApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'photo_url' => $this->photo_url,
            'mime' => $this->mime,
        ];
    }
}
