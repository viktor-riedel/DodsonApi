<?php

namespace App\Jobs\TradeMe;

use App\Actions\TradeMe\UpdateTradeMeItemListingAction;
use App\Models\TradeMeListing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateListedItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TradeMeListing $listing;

    public function __construct(TradeMeListing $listing)
    {
        $this->listing = $listing;
    }

    public function handle(): void
    {
        try {
            app()->make(UpdateTradeMeItemListingAction::class)->handle($this->listing);
        } catch (\Exception $e) {
            \Log::error('UPDATE TRADEME ITEM JOB: ' . $e->getMessage());
        }
    }
}
