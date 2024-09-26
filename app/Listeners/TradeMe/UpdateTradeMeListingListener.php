<?php

namespace App\Listeners\TradeMe;

use App\Events\TradeMe\UpdateTradeMeListingEvent;
use App\Jobs\TradeMe\UpdateListedItemJob;

class UpdateTradeMeListingListener
{
    public function handle(UpdateTradeMeListingEvent $event): void
    {
        UpdateListedItemJob::dispatch($event->listing);
    }
}
