<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MakesResource extends JsonResource
{
    public function toArray(Request $request): string
    {
        return $this->make;
    }
}
