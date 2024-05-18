<?php

namespace App\Listeners;

use App\Events\StockCars\SyncWishedCarEvent;
use App\Http\Traits\SyncWishedCarsTrait;

class SyncWishedCarsListener
{
    use SyncWishedCarsTrait;

    public function handle(SyncWishedCarEvent $event): void
    {
        $this->SyncWishedCars($event->car->id);
    }
}
