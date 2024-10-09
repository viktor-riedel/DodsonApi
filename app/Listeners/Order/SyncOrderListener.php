<?php

namespace App\Listeners\Order;

use App\Events\Order\SyncCompleteOrderEvent;
use App\Jobs\Sync\SyncPreorderDataJob;

class SyncOrderListener
{
    public function handle(SyncCompleteOrderEvent $event): void
    {
        SyncPreorderDataJob::dispatch($event->order);
    }
}
