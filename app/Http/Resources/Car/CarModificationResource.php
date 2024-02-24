<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarModificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body_type' => $this->body_type,
            'chassis' => $this->chassis,
            'generation' => $this->generation,
            'doors' => $this->doors,
            'engine_size' => $this->engine_size,
            'drive_train' => $this->drive_train,
            'header' => $this->header,
            'month_from' => $this->month_from,
            'month_to' => $this->month_to,
            'restyle' => $this->restyle,
            'transmission' => $this->transmission,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'years_string' => $this->years_string,
        ];
    }
}
