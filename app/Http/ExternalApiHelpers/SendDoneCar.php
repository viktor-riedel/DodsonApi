<?php

namespace App\Http\ExternalApiHelpers;

use Http;
use Illuminate\Http\Client\PendingRequest;

class SendDoneCar
{
    public function sendData(array $data)
    {
        try {
            $url = $this->buildUrl();
            $response = $this->prepareRequest($url)
                ->accept('application/json')
                ->withBasicAuth(config('russian_system_config.one_s_system_credentials.login'),
                    config('russian_system_config.one_s_system_credentials.password'))
                ->post('SetInventoryReceipt', $data);
            if ($response->ok()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Failed get data from 1C: ' . $e->getMessage());
        }

        return null;
    }

    private function buildUrl(): string
    {
        return config('russian_system_config.one_s_system_credentials.car_done');
    }

    private function prepareRequest(string $url): PendingRequest
    {
        return Http::baseUrl($url)->timeout(30);
    }
}
