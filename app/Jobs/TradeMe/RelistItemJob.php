<?php

namespace App\Jobs\TradeMe;

use App\Actions\TradeMe\RelistTradeMeItemAction;
use App\Models\TradeMeListing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RelistItemJob implements ShouldQueue
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
            app()->make(RelistTradeMeItemAction::class)->handle($this->listing);
        } catch (\Exception $e) {
            \Log::error('RELIST TRADEME ITEM JOB: ' . $e->getMessage());
        }
    }
}
