<?php

namespace App\Jobs\Sync;

use App\Events\Sync\Export\SendToBotEvent;
use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCarToBotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Car $car;

    public function __construct(Car $car)
    {
        $this->car  = $car;
    }

    public function handle(): void
    {
        event(new SendToBotEvent($this->car));
    }
}
