<?php

namespace App\Listeners\TradeMe;

use App\Events\TradeMe\CreateListingEvent;
use App\Jobs\TradeMe\ListItemJob;

class CreateTradeMeListingListener
{
    public function handle(CreateListingEvent $event): void
    {
        ListItemJob::dispatch($event->listing);
    }
}
