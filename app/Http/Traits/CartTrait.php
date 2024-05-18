<?php

namespace App\Http\Traits;

use App\Models\User;

trait CartTrait
{
    private function checkCartExist(User $user): void
    {
        if (!$user->cart) {
            $user->cart()->create();
            $user->refresh();
        }
    }
}
