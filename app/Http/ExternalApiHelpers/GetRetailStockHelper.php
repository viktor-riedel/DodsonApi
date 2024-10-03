<?php

namespace App\Http\ExternalApiHelpers;

use Http;
use Illuminate\Http\Client\PendingRequest;

class GetRetailStockHelper
{
    public function getStock()
    {
        try {
            $url = $this->buildUrl('getInformationStock');
            $response = $this->prepareRequest($url)
                ->accept('application/json')
                ->withBasicAuth(config('russian_system_config.one_s_system_credentials.login'),
                    config('russian_system_config.one_s_system_credentials.password'))
                ->get('');
            if ($response->ok()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Failed get data from 1C stock: ' . $e->getMessage());
        }

        return [];
    }

    private function buildUrl(string $url): string
    {
        return config('russian_system_config.one_s_system_credentials.car_done') . '/' . $url;
    }

    private function prepareRequest(string $url): PendingRequest
    {
        return Http::baseUrl($url)->timeout(180);
    }
}
