<?php

namespace App\Listeners\TradeMe;

use App\Events\TradeMe\RelistTradeMeListingEvent;
use App\Jobs\TradeMe\RelistItemJob;

class RelistTradeMeListingListener
{
    public function handle(RelistTradeMeListingEvent $event): void
    {
        RelistItemJob::dispatch($event->listing);
    }
}
