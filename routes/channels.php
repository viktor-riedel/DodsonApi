<?php

use Illuminate\Support\Facades\Broadcast;

Route::group(['middleware' => 'throttle:60,1'], function () {

    Broadcast::channel('user-channel.{id}', function ($user, $id) {
        return (int) $user->id === (int) $id;
    });

});
