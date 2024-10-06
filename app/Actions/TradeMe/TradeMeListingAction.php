<?php

namespace App\Actions\TradeMe;

use App\Http\Resources\Part\PartPhotoResource;
use App\Http\Resources\Part\TradeMeListingResource;
use App\Models\CarPdrPosition;
use App\Models\TradeMeGroup;
use App\Models\TradeMeTemplate;
use Str;

class TradeMeListingAction
{
    private CarPdrPosition $part;

    public function handle(CarPdrPosition $part): array
    {
        $this->part = $part;
        $part->load('tradeMeListing');
        $listing = null;
        if ($part->tradeMeListing) {
            $listing = new TradeMeListingResource($part->tradeMeListing);
        }
        $categories = TradeMeGroup::all();
        $duration = arrayToJsonFormat(TradeMeTemplate::DEFAULT_DURATION);
        $shipping = arrayToJsonFormat(TradeMeTemplate::SHIPPING_METHODS);
        $payments = arrayToJsonFormat(TradeMeTemplate::PAYMENT_METHODS);
        $photos = PartPhotoResource::collection($part->images);
        $tags = TradeMeTemplate::REPLACE_TAGS;
        $html = TradeMeTemplate::SUPPORTED_CHARACTERS;

        if (!$listing) {
            $listing = $this->buildTemplate();
        }

        return [
            'listing' => $listing,
            'categories' => $categories,
            'duration' => $duration,
            'shipping' => $shipping,
            'payments' => $payments,
            'tags' => $tags,
            'html' => $html,
            'photos' => $photos,
        ];
    }

    private function buildTemplate(): array
    {
        $defaultTemplate = TradeMeTemplate::first();
        $listing = [
            'id' => null,
            'title' => $this->parseTitle($defaultTemplate->title),
            'category' => null,
            'short_description' => $this->parseShortDescription($defaultTemplate->short_description),
            'description' => $this->parseDescription($defaultTemplate->description),
            'delivery_options' => $defaultTemplate->delivery_options_array,
            'default_duration' => $defaultTemplate->default_duration,
            'payments_options' => $defaultTemplate->payment_options_array,
            'update_prices' => true,
            'relist' => true,
        ];

        return $listing;
    }

    private function parseTitle(string $title): string
    {
        return $this->replaceTags($title);
    }

    private function parseShortDescription(string $shortDescription): string
    {
        return $this->replaceTags($shortDescription);
    }

    private function parseDescription(string $description): string
    {
        return $this->replaceTags($description);
    }

    private function replaceTags(string $source): string
    {
        $source = Str::replace('{part_name}', $this->part->item_name_eng, $source);
        $source = Str::replace('{make}', $this->part->carPdr->car->make, $source);
        $source = Str::replace('{model}', $this->part->carPdr->car->model, $source);
        $source = Str::replace('{year}', $this->part->carPdr->car->carAttributes->year, $source);
        $source = Str::replace('{stock}', $this->part->carPdr->car->car_mvr, $source);
        $source = Str::replace('{ic_number}', $this->part->ic_number, $source);
        $source = Str::replace('{oem_number}', $this->part->oem_number, $source);
        $source = Str::replace('{tag}', $this->part->card->barcode, $source);
        $source = Str::replace('{color}', $this->part->card->partAttributesCard->color, $source);
        $source = Str::replace('{body_style}', $this->part->carPdr->car->modification?->body_type, $source);
        $source = Str::replace('{vehicle}', $this->part->carPdr->car->chassis, $source);
        $source = Str::replace('{transmission}', $this->part->carPdr->car->modification?->transmission, $source);
        $source = Str::replace('{engine_size}', $this->part->carPdr->car->modification?->engine_size, $source);
        $source = Str::replace('{engine_code}', '', $source);
        $source = Str::replace('{description}', $this->part->ic_decsription, $source);
        $source = Str::replace('{price}', $this->part->card->priceCard->selling_price, $source);
        $source = Str::replace('(NZ ONLY)', '', $source);
        return $source;
    }
}
