<?php

namespace App\Http\Resources\BaseCar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'model_year' => $this->model_year,
            'mileage' => $this->mileage,
            'engine_type' => $this->engine_type ? explode('#', $this->engine_type) : [],
            'engine_size' => $this->engine_size,
            'power' => $this->power,
            'fuel' => $this->fuel,
            'transmission' => $this->transmission,
            'drivetrain' => $this->drivetrain,
            'color' => $this->color,
            'nomenclature' => [
                'make' => $this->nomenclatureBaseItem->make,
                'model' => $this->nomenclatureBaseItem->model,
                'generation' => $this->nomenclatureBaseItem->generation,
                'preview_image' => $this->nomenclatureBaseItem->preview_image,
                'restyle' => $this->nomenclatureBaseItem->restyle,
            ],
        ];
    }
}
