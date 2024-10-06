<?php

namespace App\Actions\TradeMe;

use App\Http\ExternalApiHelpers\TradeMeApiHelper;
use App\Models\TradeMeListing;

class UpdateTradeMeItemListingAction
{
    use TradeMeItemsTrait;

    public function handle(TradeMeListing $listing): void
    {
        $listing->load('tradeMePhotos', 'part');
        $helper = new TradeMeApiHelper();
        $itemData = $this->buildListingData($listing);
        $photos = $this->uploadItemImages($listing, $helper);
        if (count($photos)) {
            $itemData['PhotoIds'] = $photos;
        }
        $data = $helper->updateListing($itemData);
        if (isset($data['ListingId'])) {
            $listing->update([
                'listing_id' => $data['ListingId'],
                'update_date' => now(),
            ]);
        } else {
            \Log::error("Failed to update listing item. " . json_encode($data));
        }
    }
}
