<?php

namespace App\Http\Resources\StockCars;

use App\Http\Resources\Cart\LinkResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'generation' => $this->generation,
            'year' => $this->carAttributes->year &&  $this->carAttributes->year > 0 ? $this->carAttributes->year : '',
            'chassis' => $this->carAttributes->chassis,
            'color' => $this->carAttributes->color,
            'engine' => $this->carAttributes->engine,
            'mileage' => number_format($this->carAttributes->mileage),
            'price' => [
                'price_with_engine_nz' => number_format($this->carFinance?->price_with_engine_nz),
                'price_without_engine_nz' => number_format($this->carFinance?->price_without_engine_nz),
                'price_without_engine_ru' => number_format($this->carFinance?->price_without_engine_ru),
                'price_with_engine_ru' => number_format($this->carFinance?->price_with_engine_ru),
                'price_with_engine_mn' => number_format($this->carFinance?->price_with_engine_mn),
                'price_without_engine_mn' => number_format($this->carFinance?->price_without_engine_mn),
                'price_with_engine_jp' => number_format($this->carFinance?->price_with_engine_jp),
                'price_without_engine_jp' => number_format($this->carFinance?->price_without_engine_jp),
            ],
            'links' => $this->whenLoaded('links',LinkResource::collection($this->links), []),
            'comment' => $this->comment,
            'images' => $this->whenLoaded('images',
                $this->images->count() ? PhotoResource::collection($this->images) : [['url' => '/no_photo.png']]
                , [['url' => '/no_photo.png']]),
            'modification' => $this->whenLoaded('modifications',
                    new ModificationResource($this->modifications),
                null),
        ];
    }
}
