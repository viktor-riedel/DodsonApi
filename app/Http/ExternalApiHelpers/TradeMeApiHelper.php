<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\TradeMeToken;
use Http;

class TradeMeApiHelper
{

    public const API_CATEGORIES = 'Categories.json';

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
}
