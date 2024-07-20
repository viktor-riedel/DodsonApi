<?php

namespace App\Listeners;

use App\Events\Sync\Export\SendToBotEvent;
use App\Http\ExternalApiHelpers\InteractWithBot;

class SendCarToBotListener
{
    public function handle(SendToBotEvent $event): void
    {
        $apiHelper = new InteractWithBot();
        $apiHelper->notifyBotNewCar($event->car);
    }
}
