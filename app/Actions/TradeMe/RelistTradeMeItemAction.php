<?php

namespace App\Actions\TradeMe;

use App\Http\ExternalApiHelpers\TradeMeApiHelper;
use App\Models\TradeMeListing;

class RelistTradeMeItemAction
{
    use TradeMeItemsTrait;

    public function handle(TradeMeListing $listing): void
    {
        $listing->load('tradeMePhotos', 'part');
        $helper = new TradeMeApiHelper();
        $itemData = $this->getRelistData($listing);
        $data = $helper->relistItem($itemData);
        $listing->update([
            'update_date' => now(),
            'relist_date' => now(),
        ]);
    }
}
