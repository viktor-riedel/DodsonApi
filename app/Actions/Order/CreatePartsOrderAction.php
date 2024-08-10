<?php

namespace App\Actions\Order;

use App\Mail\UserPartsOrderCreatedMail;
use App\Models\CarPdrPosition;
use App\Models\Order;
use Illuminate\Http\Request;

class CreatePartsOrderAction
{
    private const DODSON_USER = 135;

    public function handle(Request $request): bool
    {
        $user = $request->user();
        $cart = $user->cart;
        $parts = collect($request->input('parts'));

        if ($parts->count()) {
            //create order and reassign a user id to all parts to a new user
            $orderNumber = Order::getNextOrderNumber();
            $order = $user->orders()->create([
                'order_number' => $orderNumber,
                'order_status' =>  Order::ORDER_STATUS_INT[Order::ORDER_STATUS_STRING[0]],
                'invoice_url' => null,
                'order_total' => 0,
                'country_code' => $user->country_code,
                'comment' => null,
            ]);
            foreach ($parts as $part) {
                $position = CarPdrPosition::find($part['id']);
                if ($position->user_id === self::DODSON_USER) {
                    $position->update([
                        'user_id' => $user->id,
                    ]);
                    $order->items()->create([
                        'car_id' => null,
                        'part_id' => $part['id'],
                        'with_engine' => false,
                        'item_name_eng' => $part['item_name_eng'],
                        'item_name_ru' => $part['item_name_ru'],
                        'price_jpy' => (int) $part['price_jpy'],
                        'user_id' => $user->id,
                        'currency' => $user->country_code,
                    ]);
                }
            }
            $order->update(['order_total' => $order->items->sum('price_jpy')]);

            //delete all from cart
            $cart->partItems()->delete();

            if ($order->items->count() === 0) {
                // no parts in the order
                $order->delete();
            } else {
                //send email
                $emails = explode(',', config('mail.info_email'));
                if (count($emails)) {
                    foreach ($emails as $email) {
                        \Mail::to($email)->send(new UserPartsOrderCreatedMail($user, $order));
                    }
                }
            }

            return true;
        }

        return false;
    }
}
