<?php

namespace App\Http\Resources\CRM\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewOrderCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inner_id' => $this->parent_inner_id,
            'mvr' => $this->car_mvr,
            'contr_agent' => $this->contr_agent_name,
            'comment' => $this->comment,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'images' => ImageResource::collection($this->images),
            'modification' => $this->modifications,
            'car_attributes' => [
                'chassis' => $this->carAttributes->chassis,
                'color' => $this->carAttributes->color,
                'year' => $this->carAttributes->year,
                'engine' => $this->carAttributes->engine,
                'mileage' => $this->carAttributes->mileage,
            ],
            'car_finances' => [
                'price_with_engine_jp' => $this->carFinance->price_with_engine_jp,
                'price_with_engine_mn' => $this->carFinance->price_with_engine_mn,
                'price_with_engine_nz' => $this->carFinance->price_with_engine_nz,
                'price_with_engine_ru' => $this->carFinance->price_with_engine_ru,
                'price_without_engine_jp' => $this->carFinance->price_without_engine_jp,
                'price_without_engine_mn' => $this->carFinance->price_without_engine_mn,
                'price_without_engine_nz' => $this->carFinance->price_without_engine_nz,
                'price_without_engine_ru' => $this->carFinance->price_without_engine_ru,
                'purchase_price' => $this->carFinance->purchase_price,
            ],
            'parts' => OrderPositionResource::collection($this->positions)
        ];
    }
}
