<?php

namespace App\Http\Resources\Car;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_inner_id' => $this->parent_inner_id,
            'mvr' => $this->car_mvr,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->carAttributes?->year,
            'car_status' => Car::CAR_STATUSES[$this->car_status],
            'generation' => $this->generation,
            'created_by' => $this->createdBy->name,
            'created_at' => $this->created_at->format('d/m/Y'),
            'images' => CarImageResource::collection($this->images),
            'chassis' =>  $this->chassis,
            'attributes' => new CarAttributeResource($this->carAttributes),
            'modification' => new CarModificationResource($this->modification),
            'parts_count' => $this->positions ? $this->positions->whereNotNull('card')->count() : 0,
            'parts_price' => $this->parts_price,
            'selling_price' => $this->selling_price,
            'car_price' => $this->carFinance->purchase_price,
            'agent' => $this->contr_agent_name,
            'sale' => $this->carFinance->car_is_for_sale,
            'price_with_engine_nz' => $this->carFinance->price_with_engine_nz,
            'price_without_engine_nz' => $this->carFinance->price_without_engine_nz,
            'price_without_engine_ru' => $this->carFinance->price_without_engine_ru,
            'price_with_engine_ru' => $this->carFinance->price_with_engine_ru,
            'price_with_engine_mn' => $this->carFinance->price_with_engine_mn,
            'price_without_engine_mn' => $this->carFinance->price_without_engine_mn,
            'price_with_engine_jp' => $this->carFinance->price_with_engine_jp,
            'price_without_engine_jp' => $this->carFinance->price_without_engine_jp,
            'is_deleted' => $this->trashed(),
            'latest_sync' => $this->latestSyncData?->document_number,
        ];
    }
}
