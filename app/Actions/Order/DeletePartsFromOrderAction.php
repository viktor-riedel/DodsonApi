<?php

namespace App\Actions\Order;

use App\Models\CarPdrPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DeletePartsFromOrderAction
{
    public function handle(Request $request): Collection
    {
        $user = $request->user();
        $cart = $user->cart;
        $parts = collect($request->input('parts', []));
        if ($parts->count()) {
            $cart->partItems()->whereIn('part_id', $parts->pluck('id'))->delete();
        }

        return CarPdrPosition::with('carPdr', 'carPdr.car',
            'carPdr.car.carAttributes', 'carPdr.car.modifications',
            'card', 'card.priceCard')
            ->whereIn('id', $cart->partItems->pluck('part_id')->toArray())
            ->get();
    }
}
