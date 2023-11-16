<?php

namespace App\Http\ExternalApiHelpers;

use Http;

class CapartsApiHelper
{

    private const ACCEPT_TYPE = 'application/json';

    public function fundCarByStockNumber(string $stock_number)
    {
        if (!cache()->has('token')) {
            $token = $this->authorize_client();
            cache()->put('token', $token, now()->addHours(24));
        } else {
            $token = cache()->get('token');
        }
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

    private function authorize_client(): string
    {
        $body = [
            'email' => config('api_helpers.login'),
            'password' => config('api_helpers.password'),
            'company_name' => config('api_helpers.caparts_company_name')
        ];
        $response = Http::
            withHeaders(
                [
                    'X-COMPANY-NAME' => config('api_helpers.caparts_company_name'),
                    'X-CSRF-TOKEN' => '',
                ]
            )->post((config('api_helpers.caparts_api_url') . '/ease-login'), $body);
        if ($response->ok()) {
            return $response->json()['access_token'];
        }
        return '';
    }
}
