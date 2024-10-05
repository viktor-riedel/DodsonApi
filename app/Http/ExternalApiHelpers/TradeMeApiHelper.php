<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\TradeMeToken;
use Http;
use Str;

class TradeMeApiHelper
{

    private const API_CATEGORIES = 'Categories.json';
    private const API_LIST_ITEM = 'Selling.json';
    private const API_UPLOAD_PHOTOS = 'Photos/Add.json';
    private const API_UPDATE_ITEM = 'Selling/Edit.json';
    private const API_RELIST_ITEM = 'Selling/Relist.json';

    public function loadCategories(): array
    {
        $categories = [];
        $data = $this->performGetRequest(self::API_CATEGORIES);
        if (isset($data['Subcategories'])) {
            foreach ($data['Subcategories'] as $category) {
                $categories[] = [
                  'name' => $category['Name'],
                  'number' => $category['Number'],
                  'path' => $category['Path'],
                ];
            }
        }
        return $categories;
    }

    public function loadSubCategories(string $path): array
    {
        $categories = [];
        $data = $this->performGetRequest(self::API_CATEGORIES . '?mcat_path=' . $path);
        if (isset($data['Subcategories'])) {
            foreach ($data['Subcategories'] as $category) {
                $categories[] = [
                    'name' => $category['Name'],
                    'number' => $category['Number'],
                    'path' => $category['Path'],
                ];
            }
        }
        return $categories;
    }

    public function uploadPhoto(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $data = [
            'PhotoData' => base64_encode(file_get_contents($path)),
            'FileName' => Str::random(10) . '.' . $extension,
            'FileType' => $extension,
        ];
        $response = $this->performPostRequest(self::API_UPLOAD_PHOTOS, $data);
        return $response['PhotoId'] ?? '';
    }

    public function createListing(array $data = []): array
    {
        return $this->performPostRequest(self::API_LIST_ITEM, $data);
    }

    public function updateListing(array $data = []): array
    {
        return $this->performPostRequest(self::API_UPDATE_ITEM, $data);
    }

    public function relistItem(array $data = []): array
    {
        return $this->performPostRequest(self::API_RELIST_ITEM, $data);
    }

    private function performGetRequest (string $path): array
    {
        $env = config('trademe.environment');
        $isSandbox = $env === 'sandbox';

        $url = $isSandbox ?
            config('trademe.sandbox_urls.api_url') :
            config('trademe.production_urls.api_url');

        $tradeMe = TradeMeToken::first();
        $secret = config('trademe.secret');
        $key = config('trademe.key');
        if ($tradeMe) {
            $accessToken = $tradeMe->access_token;
            $accessTokenSecret = $tradeMe->access_token_secret;
            $signature = $secret . '&' . $accessTokenSecret;
            $header = "OAuth oauth_consumer_key=$key, oauth_token=$accessToken, oauth_signature_method=PLAINTEXT, oauth_signature=$signature";
            return Http::withHeaders(
                    [
                        'Authorization' => $header,
                    ]
                )->get($url . '/' . $path)
                ->json();
        }
        return [];
    }

    private function performPostRequest (string $path, array $data = []): array
    {
        $env = config('trademe.environment');
        $isSandbox = $env === 'sandbox';

        $url = $isSandbox ?
            config('trademe.sandbox_urls.api_url') :
            config('trademe.production_urls.api_url');
        $tradeMe = TradeMeToken::first();
        $secret = config('trademe.secret');
        $key = config('trademe.key');

        if ($tradeMe) {
            $accessToken = $tradeMe->access_token;
            $accessTokenSecret = $tradeMe->access_token_secret;
            $signature = $secret . '&' . $accessTokenSecret;
            $header = "OAuth oauth_consumer_key=$key, oauth_token=$accessToken, oauth_signature_method=PLAINTEXT, oauth_signature=$signature";
            return Http::withHeaders(
                [
                    'Authorization' => $header,
                ]
            )->post($url . '/' . $path, $data)->json();
        }
        return [];
    }
}
