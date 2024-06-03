<?php

namespace App\Http\Resources\StockCars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'inner_id' => $this->inner_id,
            'header' => $this->header,
            'generation' => $this->generation,
            'modification' => $this->modification,
            'engine_name' => $this->engine_name,
            'engine_type' => $this->engine_type,
            'engine_size' => $this->engine_size,
            'engine_power' => $this->engine_power,
            'doors' => $this->doors,
            'transmission' => $this->transmission,
            'drive_train' => $this->drive_train,
            'chassis' => $this->chassis,
            'body_type' => $this->body_type,
            'image_url' => $this->image_url,
            'restyle' => $this->restyle,
            'not_restyle' => $this->not_restyle,
            'month_from' => $this->month_from,
            'month_to' => $this->month_to,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'years_string' => $this->years_string,
        ];
    }
}
