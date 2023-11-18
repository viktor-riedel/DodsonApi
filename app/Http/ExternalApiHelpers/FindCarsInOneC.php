<?php

namespace App\Http\ExternalApiHelpers;

use GuzzleHttp\Promise\PromiseInterface;
use Http;
use Illuminate\Http\Client\PendingRequest;

class FindCarsInOneC
{
    public function findCarByStockNumber(string $stockNumber)
    {
        try {
            $url = $this->buildUrl($stockNumber);
            $response = $this->prepareRequest($url)
                ->accept('application/json')
                ->withQueryParameters(['param' => '{"Stock":"' . $stockNumber.'"}'])
                ->withBasicAuth(config('russian_system_config.one_s_system_credentials.login'),
                    config('russian_system_config.one_s_system_credentials.password'))
                ->get('');
            if ($response->ok()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Failed get data from 1C: ' . $e->getMessage());
        }

        return null;
    }


    private function buildUrl(string $stockNumber): string
    {
        return config('russian_system_config.one_s_system_credentials.stock_num_endpoint') . '/GetCarByStock';
    }

    private function prepareRequest(string $url): PendingRequest
    {
        return Http::baseUrl($url)->timeout(30);
    }
}
