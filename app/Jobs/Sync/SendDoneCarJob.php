<?php

namespace App\Jobs\Sync;

use App\Actions\Api\GetDoneCarDataAction;
use App\Http\ExternalApiHelpers\SendDoneCar;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDoneCarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Car $car;
    public User $user;
    public SendDoneCar $httpHelper;

    public function __construct(Car $car, User $user)
    {
        $this->car = $car;
        $this->user = $user;
    }

    public function handle(): void
    {
        try {
            $data = app()->make(GetDoneCarDataAction::class)->handle($this->car);
            $this->httpHelper = new SendDoneCar();
            $response = $this->httpHelper->sendData($data);
            ray($response);
            if ($response) {
                $this->car->syncedPartsData()->create([
                    'document_number' => $response['Number'] ?? null,
                    'document_date' => $response['Date'] ?
                        Carbon::createFromFormat('d-m-Y', $response['Date'])->format('d/m/Y') :
                        null,
                    'created_by' => $this->user->id,
                ]);
            }
            // update db with server response
        } catch (\Exception $e) {
            \Log::error('SYNC DONE CAR: ' . $e->getMessage());
        }
    }
}
