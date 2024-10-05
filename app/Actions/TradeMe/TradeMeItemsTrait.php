<?php

namespace App\Actions\TradeMe;

use App\Http\ExternalApiHelpers\TradeMeApiHelper;
use App\Models\TradeMeListing;
use Str;

trait TradeMeItemsTrait
{
    private function splitDescriptionToArray(string $description): array
    {
        $split = Str::replace(PHP_EOL, '#100', $description);
        return explode('#100', $split);
    }

    private function getRelistData(TradeMeListing $listing): array
    {
        return [
            'ListingId' => $listing->listing_id,
            'ReturnListingDetails' => false,
        ];
    }

    private function buildListingData(TradeMeListing $listing): array
    {
        $data = [
            'Category' =>  $listing->category,
            'Title' => $listing->title,
            'Subtitle' => $listing->short_description,
            'Description' => $this->splitDescriptionToArray($listing->description),
            'StartPrice' => $listing->part->card->priceCard->selling_price,
            'ReservePrice' => $listing->part->card->priceCard->selling_price,
            'BuyNowPrice' => $listing->part->card->priceCard->selling_price,
            'Duration' => $listing->default_duration,
            'Pickup' => 1,
            'IsOrNearOffer' => false,
            'IsBrandNew' => false,
            'AuthenticatedMembersOnly' => false,
            'IsClassified' => false,
            'SendPaymentInstructions' => true,
            'IsPriceOnApplication' => false,
            'IsBold' => false,
            'IsFeatured' => false,
            'IsHomepageFeatured' => false,
            'HasGallery' => false,
            'HasGalleryPlus' => false,
            'IsHighlighted' => false,
            'HasSuperFeature' => false,
            'PhotoIds' => [],
        ];
        $shipping_methods = explode(',', $listing->delivery_options);
        if (count($shipping_methods)) {
            foreach($shipping_methods as $delivery) {
                $data['ShippingOptions'][] = [
                    'Type' => $delivery,
                ];
            }
        }
        $payment_methods = explode(',', $listing->payments_options);
        if (count($payment_methods)) {
            $data['PaymentMethods'] = $payment_methods;
        }

        if ($listing->listing_id) {
            $data['ListingId'] = $listing->listing_id;
        }

        return $data;
    }

    private function uploadItemImages(TradeMeListing $listing, TradeMeApiHelper $helper): array
    {
        $photosIds = [];
        if ($listing->tradeMePhotos->count()) {
            foreach($listing->tradeMePhotos as $photo) {
                $photosIds[] = $helper->uploadPhoto($photo->image_url);
            }
        }
        return $photosIds;
    }
}
