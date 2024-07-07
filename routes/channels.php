<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('online-channel', function ($user) {
    if ( $user ) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'roles' => $user->getRoleNames()->first(),
            'country_name' => findCountryByCode($user->country_code ?? ''),
            'email' => $user->email,
            'connected_time' => now()->format('d.m.Y H:m'),
        ];
    }
});
