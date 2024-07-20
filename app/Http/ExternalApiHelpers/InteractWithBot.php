<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\Car;
use Illuminate\Http\Client\PendingRequest;
use Http;
use Str;

class InteractWithBot
{
    public function notifyBotNewCar(Car $car): void
    {
        $car->load('images', 'carAttributes', 'markets', 'modifications');
        if ($car->images->count()) {
            $subject = $car->make . ' ' . $car->model . ' ' . $car->carAttributes->year;
            $markets = $car->markets->pluck('country_code')->toArray();
            if (count($markets)) {
                $availableIn = implode(', ', $markets);
            } else {
                $availableIn = '';
            }
            $subject .= ' ' . $car->modifications->header . ' ' .
                ($availableIn ? 'available for | доступно в ' . $availableIn : '');
            $data = [
                'message' => $subject,
                'photo_url' => $car->images->first()->url,
                'buttons' =>
                    [
                        [
                            'type' => 'url',
                            'text' => 'more information | показать больше',
                            'data' => [
                                'user_id' => 1,
                                'car_id' => $car->id,
                                'param' => 'show_car'
                            ],
                        ],
                    ],
            ];
            try {
                $url = $this->buildUrl();
                $this->prepareRequest($url)
                    ->accept('application/json')
                    ->post('/api/messages/send-simple-photo-message', $data);
            } catch (\Exception $e) {
                \Log::error('Failed to update telegram bot: '.$e->getMessage());
            }
        }
    }

    public function sendNotificationMessage(string $message): void
    {
        $data = [
            'message' => Str::replace(['<p>', '</p>'], '', $message),
        ];
        $url = $this->buildUrl();
        $this->prepareRequest($url)
            ->accept('application/json')
            ->post('/api/messages/send-simple-message', $data);
    }

    private function buildUrl(): string
    {
        return config('dodson_bot.bot_url');
    }

    private function prepareRequest(string $url): PendingRequest
    {
        return Http::asJson()->baseUrl($url)->timeout(30);
    }
}
