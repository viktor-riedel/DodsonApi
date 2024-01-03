<?php

namespace App\Http\Resources\BaseCar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $year_from_str = str_pad($this->month_start,2,0,STR_PAD_LEFT) . '.'.
            $this->year_start;
        if ($this->month_stop && $this->year_stop) {
            $year_end_str = str_pad($this->month_stop,2,0,STR_PAD_LEFT) . '.'.
                $this->year_stop;
        } else {
            $year_end_str = 'now';
        }

        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'generation_number' => $this->generation_number,
            'body_type' => $this->body_type,
            'doors' => $this->doors,
            'month_start' => $this->month_start,
            'month_stop' => $this->month_stop,
            'year_start' => $this->year_start,
            'year_stop' => $this->year_stop,
            'restyle' => $this->restyle,
            'not_restyle' => $this->not_restyle,
            'header' => $this->header,
            'years_string' => $year_from_str . '-' . $year_end_str,
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
