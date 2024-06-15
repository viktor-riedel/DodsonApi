<?php

namespace App\Listeners;

use App\Events\Sync\Export\SendToBotEvent;
use App\Http\ExternalApiHelpers\SendListedCarToBot;

class SendCarToBotListener
{
    public function handle(SendToBotEvent $event): void
    {
        $apiHelper = new SendListedCarToBot($event->car);
        $apiHelper->notifyBot();
    }
}
