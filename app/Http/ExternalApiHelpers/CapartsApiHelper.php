<?php

namespace App\Http\ExternalApiHelpers;

use Http;

class CapartsApiHelper
{

    private const ACCEPT_TYPE = 'application/json';
    private const CACHE_ACCESS_TOKEN = 'access_token';

    public function fundCarByStockNumber(string $stock_number)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->accept(self::ACCEPT_TYPE)
            ->withHeaders(
                [
                    'X-COMPANY-NAME' => config('api_helpers.caparts_company_name'),
                    'X-CSRF-TOKEN' => '',
                ]
            )
            ->get(config('api_helpers.caparts_api_url') . '/cars/find-car-stock/' . $stock_number);
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return null;
    }

    public function importUnsoldCars()
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->accept(self::ACCEPT_TYPE)
            ->withHeaders(
                [
                    'X-COMPANY-NAME' => config('api_helpers.caparts_company_name'),
                    'X-CSRF-TOKEN' => '',
                ]
            )
            ->timeout(60)
            ->get(config('api_helpers.caparts_api_url') . '/cars/export-latest-unsold-cars');
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return null;
    }

    private function authorize_client(): string
    {
        if (cache()->has(self::CACHE_ACCESS_TOKEN)) {
            return cache()->get(self::CACHE_ACCESS_TOKEN);
        }

        $body = [
            'email' => config('api_helpers.login'),
            'password' => config('api_helpers.password'),
            'company_name' => config('api_helpers.caparts_company_name')
        ];
        $response = Http::withHeaders(
            [
                'X-COMPANY-NAME' => config('api_helpers.caparts_company_name'),
                'X-CSRF-TOKEN' => '',
            ]
        )->post((config('api_helpers.caparts_api_url') . '/ease-login'), $body);
        if ($response->ok()) {
            $token = $response->json()['access_token'];
            cache()->put(self::CACHE_ACCESS_TOKEN, $token, now()->addHours(24));
            return $token;
        }
        return '';
    }
}
