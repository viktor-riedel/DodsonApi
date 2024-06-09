<?php

namespace App\Events\Sync\Export;

use App\Models\Car;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarDoneEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $done = false;
    public bool $busy = false;
    public int $carId = 0;
    public int $userId = 0;
    public string $documentDate = '';
    public string $documentNumber = '';

    public function __construct(User $user,
        Car $car,
        bool $busy = false,
        bool $done = false,
        string $documentDate = '',
        string $documentNumber = '',
    )
    {
        $this->carId = $car->id;
        $this->busy = $busy;
        $this->done = $done;
        $this->userId = $user->id;
        $this->documentNumber = $documentNumber;
        $this->documentDate = $documentDate;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'parts-csv-export-complete';
    }
}
