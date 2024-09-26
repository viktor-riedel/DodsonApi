<?php

namespace App\Console\Commands;

use App\Events\TradeMe\RelistTradeMeListingEvent;
use App\Models\Part;
use Illuminate\Console\Command;

class TradeMeListingRelistCommand extends Command
{
    protected $signature = 'trademe-listing:relist';

    protected $description = 'This command checks if a listing needs to be relisted';

    public function handle(): void
    {
        if (config('app.env') === 'production') {
            $parts = Part::whereHas('tradeMeListing', function ($query) {
                return $query->where('listing_id', '>', 0)
                    ->where('relist', true);
            })->get();
            foreach ($parts as $part) {
                //check if created not even relisted
                if (!$part->tradeMeListing->relist_date) {
                    $relistDate = $part->tradeMeListing->created_at->addDays($part->tradeMeListing->default_duration);
                    if (now()->isSameDay($relistDate)) {
                        event(new RelistTradeMeListingEvent($part->tradeMeListing));
                    }
                } else {
                    //check if relisted
                    $relistDate = $part->tradeMeListing->relist_date->addDays($part->tradeMeListing->default_duration);
                    if (now()->isSameDay($relistDate)) {
                        event(new RelistTradeMeListingEvent($part->tradeMeListing));
                    }
                }
            }
        } else {
            $this->info('Application is not in production mode');
        }
    }
}
