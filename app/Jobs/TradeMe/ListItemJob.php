<?php

namespace App\Jobs\TradeMe;

use App\Actions\TradeMe\ListTradeMeItemAction;
use App\Models\TradeMeListing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ListItemJob implements ShouldQueue
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
            app()->make(ListTradeMeItemAction::class)->handle($this->listing);
        } catch (\Exception $e) {
            \Log::error('LIST TRADEME ITEM JOB: ' . $e->getMessage());
        }
    }
}
