<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\Car;
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
        $this->car->load('images', 'carAttributes', 'markets', 'modifications');
        if ($this->car->images->count()) {
            $subject = $this->car->make . ' ' . $this->car->model . ' ' . $this->car->carAttributes->year;
            $markets = $this->car->markets->pluck('country_code')->toArray();
            if (count($markets)) {
                $availableIn = implode(', ', $markets);
            } else {
                $availableIn = '';
            }
            $subject .= ' ' . $this->car->modifications->header . ' ' .
                ($availableIn ? 'available for / доступно в ' . $availableIn : '');
            $data = [
                'message' => $subject,
                'photo_url' => $this->car->images->first()->url,
                'buttons' =>
                    [
                        [
                            'type' => 'url',
                            'text' => 'more information / показать больше',
                            'data' => [
                                'user_id' => 1,
                                'car_id' => $this->car->id,
                                'param' => 'show_car'
                            ],
                        ],
                    ],
            ];
            try {
                \Log::info(json_encode($data));
                $url = $this->buildUrl();
                $response = $this->prepareRequest($url)
                    ->accept('application/json')
                    ->post('/api/messages/send-simple-photo-message', $data);
                if ($response->ok()) {
                    //update
                }
            } catch (\Exception $e) {
                \Log::error('Failed to update telegram bot: '.$e->getMessage());
            }
        }
    }

    private function buildUrl(): string
    {
        return config('dodson_bot.bot_url');
    }

    private function prepareRequest(string $url): PendingRequest
    {
        return Http::baseUrl($url)->timeout(30);
    }
}
