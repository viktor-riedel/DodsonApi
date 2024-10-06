<?php

namespace App\Actions\Order;

use App\Models\CarPdrPosition;
use App\Models\User;

class DeletePartFromOrderAction
{
    public function handle(User $user, CarPdrPosition $part): void
    {
        $cart = $user->cart;
        $cart->partItems()->where('part_id', $part->id)->first()?->delete();
    }
}
