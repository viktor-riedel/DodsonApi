<?php

namespace App\Events\Sync\Export;

use App\Models\Car;
use Illuminate\Foundation\Events\Dispatchable;

class SendToBotEvent
{
    use Dispatchable;

    public Car $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }
}
