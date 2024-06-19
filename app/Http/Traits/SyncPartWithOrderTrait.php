<?php

namespace App\Http\Traits;

use App\Models\Car;
use App\Models\CarPdrPosition;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

trait SyncPartWithOrderTrait
{
    private function addPartToOrder(Car $car, int $userId, CarPdrPosition $position): void
    {
        $item = OrderItem::with('order')->where([
            'car_id' => $car->id,
            'user_id' => $userId
        ])->first();
        // any order has been created for this car we add part
        if ($item) {
            OrderItem::create([
                'order_id' => $item->order->id,
                'car_id' => $item->car_id,
                'user_id' => $userId,
                'part_id' => null,
                'with_engine' => false,
                'item_name_eng' => $position->item_name_eng,
                'item_name_ru' => $position->item_name_ru,
                'price_jpy' => 0,
                'comment' => null,
            ]);
            $item->order->update(['order_total' => $item->order->items->sum('price_jpy')]);
        } else {
            //create order for that client
            $orderNumber = Order::getNextOrderNumber();
            $user = User::find($userId);
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_status' =>  Order::ORDER_STATUS_INT[Order::ORDER_STATUS_STRING[0]],
                'invoice_url' => null,
                'order_total' => 0,
                'country_code' => $user->country_code,
                'comment' => null,
            ]);
            $order->items()->create([
                'car_id' => $car->id,
                'part_id' => null,
                'with_engine' => false,
                'item_name_eng' => $position->item_name_eng,
                'item_name_ru' => $position->item_name_ru,
                'price_jpy' => $position->card->priceCard->buying_price ?? 0,
                'user_id' => $userId,
                'currency' => 'JPY',
            ]);
            $order->update(['order_total' => $order->items->sum('price_jpy')]);
        }
    }

    private function deletePartFromOrder(Car $car, int $userId, CarPdrPosition $position): void
    {
        //get items
        $item = OrderItem::with('order')
            ->where([
                'car_id' => $car->id,
                'item_name_eng' => $position->item_name_eng,
                'user_id' => $userId
                ])
            ->first();
        if ($item) {
            //get order
            $order = $item->order;
            $item->delete();
            //check if order is empty
            if (!$order->items->count()) {
                $order->delete();
            }
        }
    }

    private function updatePriceForPartInOrder(Car $car, int $userId, CarPdrPosition $position): void
    {
        $item = OrderItem::with('order')->where([
            'car_id' => $car->id,
            'item_name_eng' => $position->item_name_eng,
            'user_id' => $userId
        ])->first();

        if ($item) {
            $item->update(['price_jpy' => $position->card->priceCard->buying_price]);
            $item->order->update(['order_total' => $item->order->items->sum('price_jpy')]);
        }
    }
}
