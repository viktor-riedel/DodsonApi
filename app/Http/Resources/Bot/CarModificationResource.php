<?php

namespace App\Http\Resources\Bot;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarModificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'header' => $this->header,
            'generation' => $this->generation,
            'engine_name' => $this->engine_name,
            'engine_type' => $this->engine_type,
            'engine_size' => $this->engine_size,
            'engine_power' => $this->engine_power,
            'doors' => $this->doors,
            'transmission' => $this->transmission,
            'drive_train' => $this->drive_train,
            'chassis' => $this->chassis,
            'body_type' => $this->body_type,
            'restyle' => $this->restyle,
            'not_restyle' => $this->not_restyle,
            'years_string' => $this->years_string,
        ];
    }
}
