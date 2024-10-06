<?php

namespace App\Actions\Order;

use App\Models\CarPdrPosition;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AddPartsToOrderAction
{
    public function handle(Request $request): Collection
    {
        $cart = $request->user()->cart;
        if (!$cart) {
            $cart = $request->user()->cart()->create([]);
        }
        $parts = collect($request->input('parts', []));

        //check parts which might be added to the cart by other users
        $alreadyAdded = CartItem::whereIn('part_id', $parts->pluck('id')->toArray())
            ->where('user_id', '!=', $request->user())
            ->get();

        //filter parts
        if ($alreadyAdded->count()) {
            $parts = $parts->filter(function ($part) use ($alreadyAdded) {
               return !in_array($part['id'], $alreadyAdded->pluck('id')->toArray(), true);
            });
        }
        foreach ($parts as $part) {
            $cart->cartItems()->create([
                'user_id' => $request->user()->id,
                'part_id' => (int) $part['id'],
            ]);
        }

        return CarPdrPosition::with('carPdr', 'carPdr.car',
            'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard')
            ->whereIn('id', $cart->partItems->pluck('part_id')->toArray())
            ->get();
    }
}
