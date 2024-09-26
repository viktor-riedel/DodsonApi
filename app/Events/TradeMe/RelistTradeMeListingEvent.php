<?php

namespace App\Events\TradeMe;

use App\Models\TradeMeListing;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RelistTradeMeListingEvent
{
    use Dispatchable, SerializesModels;

    public TradeMeListing $listing;

    public function __construct(TradeMeListing $listing)
    {
        $this->listing = $listing;
    }
}
