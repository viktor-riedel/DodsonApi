<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenerationsResource extends JsonResource
{
    public function toArray(Request $request): string
    {
        return $this['generation'];
    }
}
