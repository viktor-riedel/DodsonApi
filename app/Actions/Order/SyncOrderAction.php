<?php

namespace App\Actions\Order;

use App\Http\ExternalApiHelpers\SyncOrdersHelper;
use App\Http\Resources\Car\CarResource;
use App\Http\Resources\CRM\Orders\OrderResource;
use App\Http\Resources\Order\OrderItemResource;
use App\Models\CarPdrPosition;
use App\Models\Order;

class SyncOrderAction
{
    public function handle(Order $order): void
    {
        $syncHelper = new SyncOrdersHelper();
        $isPartsOrder = $order->items->first()->car === null;

        //sync cars order
        if (!$isPartsOrder) {
            $order->load('items', 'createdBy');
            $car = $order->items->first()->car;
            $car->load('images', 'carAttributes', 'modifications');
            $data = [
                'client' => [
                    'name' => $order->createdBy->name,
                    'registered_name' => $order->createdBy->userCard->trading_name,
                ],
                'id' => $car->id,
                'make' => $car->make,
                'model' => $car->model,
                'year' => $car->carAttributes->year,
                'chassis' => $car->chassis,
                'vin' => null,
                'modification' => $car->modifications?->body_type,
                'header' => $car->modifications?->header,
                'shape' => $car->modifications?->modification,
                'generation_shape' => null,
                'generation_number' => $car->modifications?->generation,
                'restyle' => $car->modifications?->restyle,
                'variant' => null,
                'drive_train' => $car->modifications?->drive_trail,
                'color' => null,
                'mileage' => $car->carAttributes->mileage,
                'year_from' => $car->modifications?->year_from,
                'year_to' => $car->modifications?->year_to,
                'engine_size' => $car->modifications?->engine_size,
                'engine_name' => $car->modifications?->engine_name,
                'engine_type' => $car->modifications?->engine_type,
                'fuel' => null,
                'engine_oem_number' => null,
                'engine_ic_number' => null,
                'engine_power' => $car->modifications?->engine_power,
                'transmission_type' => $car->modifications?->transmission,
                'transmission_name' => null,
                'transmission_ic_number' => null,
                'transmission_oem_number' => null,
                'doors' => $car->modifications?->doors,
                'auction_source' => $car->contr_agent_name,
                'price' => $car->carFinance->purchase_price,
                'stock_number' => $car->car_mvr,
                'catalog_mvr_id' => null,
                'generation_id' => null,
                'modification_id' => $car->modifications?->inner_id,
                'images' => $car->images->map(function ($image) {
                    return $image->url;
                })->flatten()->toArray(),
                'total' => $order->items->sum('price_jpy'),
            ];

            $syncData = $syncHelper->sendPreOrderData($data);
            
            if (isset($syncData['ResultData']) && $syncData['Res']) {
                // update order sync data
            }
        }

        //sync parts order
    }
}
