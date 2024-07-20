<?php

namespace App\Listeners\Bot;

use App\Events\Bot\SendBotMessageEvent;
use App\Http\ExternalApiHelpers\InteractWithBot;

class SendBotMessageListener
{
    public function handle(SendBotMessageEvent $event): void
    {
        $apiHelper = new InteractWithBot();
        $apiHelper->sendNotificationMessage($event->message);

    }
}
