<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'group_name' => $this->part_group,
        ];
    }
}
