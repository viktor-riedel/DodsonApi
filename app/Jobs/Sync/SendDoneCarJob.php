<?php

namespace App\Jobs\Sync;

use App\Actions\Api\GetDoneCarDataAction;
use App\Http\ExternalApiHelpers\SendDoneCar;
use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDoneCarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Car $car;
    public SendDoneCar $httpHelper;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    public function handle(): void
    {
        try {
            $data = app()->make(GetDoneCarDataAction::class)->handle($this->car);
            $this->httpHelper = new SendDoneCar();
            $response = $this->httpHelper->sendData($data);
            // update db with server response
        } catch (\Exception $e) {
            dump($e);
            \Log::error('SYNC DONE CAR: ' . $e->getMessage());
        }
    }
}
