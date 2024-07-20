<?php

namespace App\Events\Bot;

use Illuminate\Foundation\Events\Dispatchable;

class SendBotMessageEvent
{
    use Dispatchable;

    public string $message = '';

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
