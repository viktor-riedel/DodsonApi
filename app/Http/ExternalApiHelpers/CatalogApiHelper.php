<?php

namespace App\Http\ExternalApiHelpers;

use Http;

class CatalogApiHelper
{

    private const ACCEPT_TYPE = 'application/json';
    private const CACHE_TOKEN = 'catalog_token';

    public function loadMakes()
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->accept(self::ACCEPT_TYPE)
            ->get(config('api_helpers.catalog_api_url') . '/catalog/makes');
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }


    public function loadModels(int $make_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/makes/$make_id/models");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    public function loadGenerations(int $model_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/model/$model_id/generations");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    public function loadModifications(int $model_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/model/$model_id/modifications");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    public function loadModificationsByHeader(int $model_id, int $header_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/model/$model_id/modifications-by-header/$header_id");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }


    public function loadModelMvrs(int $model_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/model/$model_id/model-mvrs");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    public function loadPdr(int $mvr_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/mvr-pdr/" . $mvr_id);
        if ($response->ok()) {
            return $response->json();
        }

        return response()->json([]);
    }

    public function loadModelMvr(int $model_id, int $mvr_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/model/$model_id/mvr/$mvr_id");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    public function loadMvrHeaders(int $make_id, int $model_id)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/mvr-headers/$make_id/$model_id");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    public function findMvrHeadersByMakeModelGeneration(string $make, string $model, string $generation)
    {
        $token = $this->authorize_client();
        $response = Http::withToken($token)
            ->get(config('api_helpers.catalog_api_url') . "/catalog/find-mvr-headers/$make/$model/$generation");
        if ($response->ok()) {
            return $response->json()['data'];
        }

        return response()->json([]);
    }

    private function authorize_client(): string
    {
        if (cache()->has(self::CACHE_TOKEN)) {
            return cache()->get(self::CACHE_TOKEN);
        }

        $body = [
            'email' => config('api_helpers.login'),
            'password' => config('api_helpers.password'),
        ];
        $response = Http::post(config('api_helpers.catalog_api_url') . '/login', $body);
        if ($response->ok()) {
            $token = $response->json()['data']['token'];
            cache()->put(self::CACHE_TOKEN, $token, now()->addHours(24));
            return $token;
        }
        return '';
    }
}
