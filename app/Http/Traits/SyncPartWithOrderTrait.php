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
                'price_jpy' => $position->card->priceCard->buying_price ?? 0,
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

    private function syncOrdersWithDoneResponse(Car $car, array $invoices): void
    {
        if (count($invoices)) {
            foreach ($invoices as $invoice) {
                $user = User::where('name', $invoice['Company'] ?? '')->first();
                if ($user) {
                    $items = OrderItem::with('order')
                        ->where('car_id', $car->id)
                        ->where('user_id', $user->id)
                        ->get();
                    $order = $items->first()->order;
                    //update order
                    $order->update([
                        'order_number' => $invoice['Number'],
                        'status_ru' => $invoice['State'],
                        'status_en' => null,
                        'order_total' => $invoice['TotalAmount'] > 0 ? $invoice['TotalAmount'] : $order->order_total,
                        'reference' => $invoice['Ref'],
                        'mvr_price' => $invoice['MVRPrice'],
                        'extra_price' => $invoice['Number'],
                        'package_price' => $invoice['DJP_Package'],
                        'mvr_commission' => $invoice['MVRСommission'],
                        'currency' => $invoice['Currency'],
                    ]);
                    // update order items
                    if (isset($invoice['Inventory']) && count($invoice['Inventory'])) {
                        foreach($invoice['Inventory'] as $inventoryItem) {
                            $value = $inventoryItem['Item'] ?? null;
                            if ($value) {
                                $orderItem = $items->where('item_name_eng', $value['Description'])->first();
                                if (!$orderItem) {
                                    $orderItem = $items->where('item_name_ru', $value['Description'])->first();
                                }
                                $orderItem?->update([
                                        'item_status_ru' => $inventoryItem['State'],
                                        'item_status_en' => '',
                                ]);
                                if ($inventoryItem['Price']) {
                                    $orderItem?->update([
                                        'price_jpy' => $inventoryItem['Price'],
                                    ]);
                                }
                            }
                        }
                    } else {
                        \Log::warning('Warning! No inventory items for order: ' . $order->id . ' in response!');
                    }
                } else {
                    \Log::error('Error syncing with 1C. User not found by name: ' . $invoice['Company']);
                }
            }
        } else {
            \Log::error('Error syncing with 1C. Invoices count = 0 for car: ' . $car->id);
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
