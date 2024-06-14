<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\Car;
use Cache;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SendListedCarToBot
{
    private Car $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    public function notifyBot(): void
    {
        $token = Cache::get('bot-token');
        if (!$token) {

        }
    }

    private function getBotToken(): string
    {
        $url =  '';
        return '';
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
