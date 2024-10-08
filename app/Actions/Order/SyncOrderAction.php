<?php

namespace App\Actions\Order;

use App\Http\ExternalApiHelpers\SyncOrdersHelper;
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
                'base_car' => [
                    'inner_id' => $car->modifications?->inner_id,
                    'make' => $car->make,
                    'model' => $car->model,
                    'base_modification' => [
                        'inner_id' => $car->modifications?->inner_id,
                        'header' => $car->modifications?->header,
                        'generation' => $car->modifications?->generation,
                        'engine_name' => $car->modifications?->engine_name,
                        'engine_type' => $car->modifications?->body_type,
                        'engine_size' => $car->modifications?->engine_size,
                        'engine_power' => $car->modifications?->engine_power,
                        'doors' => $car->modifications?->doors,
                        'transmission' => $car->modifications?->transmission,
                        'drive_train' => $car->modifications?->drive_train,
                        'chassis' => $car->modifications?->chassis,
                        'body_type' => $car->modifications?->body_type,
                        'restyle' => $car->modifications?->restyle,
                        'month_from' => $car->modifications?->month_from,
                        'month_to' => $car->modifications?->month_to,
                        'year_from' => $car->modifications?->year_from,
                        'year_to' => $car->modifications?->year_to,
                    ],
                ],
                'car' => [
                    'make' => $car->make,
                    'model' => $car->model,
                    'year' => $car->carAttributes->year,
                    'chassis' => $car->chassis,
                    'color' => null,
                    'mileage' => $car->carAttributes->mileage,
                    'mvr' => [
                        'mvr' => $car->car_mvr,
                    ],
                    'generation' => $car->generation,
                    'engine' => $car->carAttributes->engine,
                ],
                'finance' => [
                    'total' => $order->items->sum('price_jpy'),
                ],
                'images' => $car->images->map(function ($image) {
                    return $image->url;
                })->flatten()->toArray(),
                'client' => [
                    'name' => $order->createdBy->name,
                    'registered_name' => $order->createdBy->userCard->trading_name,
                    'email' => $order->createdBy->email,
                ],
            ];
            $syncData = $syncHelper->sendPreOrderData($data);

            if (isset($syncData['ResultData']) && $syncData['Res']) {
                // update order sync data
            }
        } else {
            $order->load('items', 'createdBy');
            foreach($order->items as $item) {
                $car = CarPdrPosition::find($item->part_id)->carPdr->car;
                $car->load('images', 'carAttributes', 'modifications');
                $data = [
                    'base_car' => [
                        'inner_id' => $car->modifications?->inner_id,
                        'make' => $car->make,
                        'model' => $car->model,
                        'base_modification' => [
                            'inner_id' => $car->modifications?->inner_id,
                            'header' => $car->modifications?->header,
                            'generation' => $car->modifications?->generation,
                            'engine_name' => $car->modifications?->engine_name,
                            'engine_type' => $car->modifications?->body_type,
                            'engine_size' => $car->modifications?->engine_size,
                            'engine_power' => $car->modifications?->engine_power,
                            'doors' => $car->modifications?->doors,
                            'transmission' => $car->modifications?->transmission,
                            'drive_train' => $car->modifications?->drive_train,
                            'chassis' => $car->modifications?->chassis,
                            'body_type' => $car->modifications?->body_type,
                            'restyle' => $car->modifications?->restyle,
                            'month_from' => $car->modifications?->month_from,
                            'month_to' => $car->modifications?->month_to,
                            'year_from' => $car->modifications?->year_from,
                            'year_to' => $car->modifications?->year_to,
                        ],
                    ],
                    'car' => [
                        'make' => $car->make,
                        'model' => $car->model,
                        'year' => $car->carAttributes->year,
                        'chassis' => $car->chassis,
                        'color' => null,
                        'mileage' => $car->carAttributes->mileage,
                        'mvr' => [
                            'mvr' => $car->car_mvr,
                        ],
                        'generation' => $car->generation,
                        'engine' => $car->carAttributes->engine,
                    ],
                    'finance' => [
                        'total' => $order->items->sum('price_jpy'),
                    ],
                    'images' => $car->images->map(function ($image) {
                        return $image->url;
                    })->flatten()->toArray(),
                    'client' => [
                        'name' => $order->createdBy->name,
                        'registered_name' => $order->createdBy->userCard->trading_name,
                        'email' => $order->createdBy->email,
                    ],
                ];
                $syncData = $syncHelper->sendPreOrderData($data);
                if (isset($syncData['ResultData']) && $syncData['Res']) {
                    // update order sync data
                }
            }
        }

        //sync parts order
    }
}
