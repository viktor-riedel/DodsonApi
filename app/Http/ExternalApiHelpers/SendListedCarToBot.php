<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\Car;
use Illuminate\Http\Client\PendingRequest;
use Http;

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
                ($availableIn ? 'available for | доступно в ' . $availableIn : '');
            $data = [
                'message' => $subject,
                'photo_url' => $this->car->images->first()->url,
                'buttons' =>
                    [
                        [
                            'type' => 'url',
                            'text' => 'more information | показать больше',
                            'data' => [
                                'user_id' => 1,
                                'car_id' => $this->car->id,
                                'param' => 'show_car'
                            ],
                        ],
                    ],
            ];
            try {
                $url = $this->buildUrl();
                $response = $this->prepareRequest($url)
                    ->accept('application/json')
                    ->post('/api/messages/send-simple-photo-message',
                        json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                if ($response->ok()) {
                    //update
                    \Log::info('Car sent to bot: ' . $this->car->id);
                } else {
                    \Log::error('Failed to update telegram bot: '. $response->toException()?->getMessage());
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
