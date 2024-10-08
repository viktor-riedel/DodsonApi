<?php

namespace App\Http\Traits;

use App\Models\CarPdrPositionCard;
use App\Models\OrderItem;

trait SyncPartsPricesTrait
{
    private function syncPartPrice(CarPdrPositionCard $card, int $price, $userId): void
    {
        $car = $card->position->carPdr->car;
        $part = $card->position;
        $orderItem = OrderItem::with('order')
            ->where('car_id', $car->id)
            ->where('user_id', $userId)
            ->where('item_name_eng', $part->item_name_eng)
            ->first();
        if ($orderItem) {
            $orderItem->update([
                'price_jpy' => $price,
            ]);
            $orderItem->order()->update([
               'total_amount' => $orderItem->order->items->sum('price_jpy'),
            ]);
        }
    }
}
