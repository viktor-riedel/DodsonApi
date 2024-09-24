<?php

namespace App\Actions\TradeMe;

use App\Http\Resources\Part\PartPhotoResource;
use App\Http\Resources\Part\TradeMeListingResource;
use App\Models\Part;
use App\Models\TradeMeGroup;
use App\Models\TradeMeTemplate;
use Illuminate\Support\Collection;
use Str;

class TradeMeListingAction
{
    private Part $part;

    public function handle(Part $part): array
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
        $source = Str::replace('{make}', $this->part->make, $source);
        $source = Str::replace('{model}', $this->part->model, $source);
        $source = Str::replace('{year}', $this->part->year, $source);
        $source = Str::replace('{tag}', $this->part->stock_number, $source);
        $source = Str::replace('{color}', $this->part->color, $source);
        $source = Str::replace('{body_style}', $this->part->body_style, $source);
        $source = Str::replace('{vehicle}', $this->part->vehicle, $source);
        $source = Str::replace('{transmission}', $this->part->transmission, $source);
        $source = Str::replace('{engine_size}', $this->part->engine_size, $source);
        $source = Str::replace('{engine_code}', $this->part->engine_code, $source);
        $source = Str::replace('{description}', $this->part->ic_decsription, $source);
        $source = Str::replace('{price}', $this->part->actual_price_nzd, $source);
        $source = Str::replace('(NZ ONLY)', '', $source);
        return $source;
    }
}
